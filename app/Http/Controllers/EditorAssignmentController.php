<?php

namespace App\Http\Controllers;

use App\Models\EditorAssignment;
use App\Models\ConferenceYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EditorAssignmentController extends Controller
{
    /**
     * Display a listing of editor assignments for a specific year
     */
    public function index($year): JsonResponse
    {
        try {
            $assignments = EditorAssignment::with('user')
                ->where('conference_year_id', $year)
                ->get();

            return $this->success(['data' => $assignments], 'Editor assignments boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Editor assignments listing failed', 'Načítanie editor assignments zlyhalo');
        }
    }

    /**
     * Store a newly created editor assignment
     */
    public function store(Request $request, $year): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($request, $year) {
            $errors = $this->validateRequest($request, [
                'user_id' => 'required|exists:users,id'
            ], [
                'user_id.required' => 'Používateľ je povinný',
                'user_id.exists' => 'Vybraný používateľ neexistuje'
            ]);

            if ($errors) {
                return $this->error('Neplatné údaje', $errors, 422);
            }

            // Check if conference year exists
            $conferenceYear = ConferenceYear::find($year);
            if (!$conferenceYear) {
                return $this->error('Ročník konferencie neexistuje', [
                    'conference_year_id' => ['Vybraný ročník konferencie neexistuje']
                ], 422);
            }

            // Check if assignment already exists
            $existingAssignment = EditorAssignment::where('user_id', $request->user_id)
                ->where('conference_year_id', $year)
                ->first();

            if ($existingAssignment) {
                return $this->error('Editor assignment už existuje', [
                    'assignment' => ['Tento editor už je priradený k tomuto ročníku konferencie']
                ], 422);
            }

            $assignment = EditorAssignment::create([
                'user_id' => $request->user_id,
                'conference_year_id' => $year
            ]);

            return $this->success(['data' => $assignment->load('user')], 'Editor assignment bol úspešne vytvorený', 201);
        }, 'Vytvorenie editor assignment zlyhalo');
    }

    /**
     * Remove the specified editor assignment
     */
    public function destroy($year, EditorAssignment $assignment): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($assignment) {
            $assignment->delete();

            return $this->success([], 'Editor assignment bol úspešne vymazaný', 204);
        }, 'Vymazanie editor assignment zlyhalo');
    }

    /**
     * Get assignments for the current authenticated user
     */
    public function myAssignments(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $assignments = EditorAssignment::with(['conferenceYear'])
                ->where('user_id', $user->id)
                ->get();

            return $this->success(['data' => $assignments], 'Moje assignments boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'My assignments failed', 'Načítanie mojich assignments zlyhalo');
        }
    }

    /**
     * Get all editor assignments with users and conference years
     */
    public function all(): JsonResponse
    {
        try {
            $assignments = EditorAssignment::with(['user', 'conferenceYear'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->success(['data' => $assignments], 'Všetky editor assignments boli úspešne načítané');
        } catch (\Exception $e) {
            return $this->handleException($e, 'All assignments failed', 'Načítanie všetkých assignments zlyhalo');
        }
    }

    /**
     * Get assignments by user
     */
    public function byUser($userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            $assignments = EditorAssignment::with(['conferenceYear'])
                ->where('user_id', $userId)
                ->get();

            return $this->success([
                'data' => $assignments,
                'user' => $user
            ], "Assignments pre používateľa {$user->username} boli úspešne načítané");
        } catch (\Exception $e) {
            return $this->handleException($e, 'User assignments failed', 'Načítanie assignments pre používateľa zlyhalo');
        }
    }

    /**
     * Get available users for assignment (users not yet assigned to the conference year)
     */
    public function availableUsers($year): JsonResponse
    {
        try {
            $assignedUserIds = EditorAssignment::where('conference_year_id', $year)
                ->pluck('user_id')
                ->toArray();

            $availableUsers = User::whereNotIn('id', $assignedUserIds)
                ->orderBy('username')
                ->get(['id', 'username', 'email']);

            return $this->success(['data' => $availableUsers], 'Dostupní používatelia boli úspešne načítaní');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Available users failed', 'Načítanie dostupných používateľov zlyhalo');
        }
    }

    /**
     * Bulk assign users to conference year
     */
    public function bulkAssign(Request $request, $year): JsonResponse
    {
        return $this->executeWithTransaction(function() use ($request, $year) {
            $errors = $this->validateRequest($request, [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ], [
                'user_ids.required' => 'Používatelia sú povinní',
                'user_ids.array' => 'Používatelia musia byť v poli',
                'user_ids.*.exists' => 'Jeden alebo viac vybraných používateľov neexistuje'
            ]);

            if ($errors) {
                return $this->error('Neplatné údaje', $errors, 422);
            }

            // Check if conference year exists
            $conferenceYear = ConferenceYear::find($year);
            if (!$conferenceYear) {
                return $this->error('Ročník konferencie neexistuje', [
                    'conference_year_id' => ['Vybraný ročník konferencie neexistuje']
                ], 422);
            }

            $userIds = $request->user_ids;
            $createdAssignments = [];
            $skippedUsers = [];

            foreach ($userIds as $userId) {
                // Check if assignment already exists
                $existingAssignment = EditorAssignment::where('user_id', $userId)
                    ->where('conference_year_id', $year)
                    ->first();

                if (!$existingAssignment) {
                    $assignment = EditorAssignment::create([
                        'user_id' => $userId,
                        'conference_year_id' => $year
                    ]);
                    $createdAssignments[] = $assignment->load('user');
                } else {
                    $skippedUsers[] = $userId;
                }
            }

            $message = count($createdAssignments) . ' assignments bolo vytvorených';
            if (count($skippedUsers) > 0) {
                $message .= ', ' . count($skippedUsers) . ' bolo preskočených (už existovali)';
            }

            return $this->success([
                'data' => $createdAssignments,
                'created_count' => count($createdAssignments),
                'skipped_count' => count($skippedUsers),
                'skipped_users' => $skippedUsers
            ], $message, 201);
        }, 'Bulk assignment zlyhalo');
    }
}
