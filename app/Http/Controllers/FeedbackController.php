<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * Store feedback from a cook.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:suggestion,error',
            'message' => 'required|string|max:2000',
        ]);

        try {
            $this->feedbackService->createFeedback(auth()->user(), $request->only('type', 'message'));

            if ($request->wantsJson()) {
                return response()->json(['message' => '¡Gracias! Tu feedback ha sido enviado correctamente.'], 200);
            }

            return back()->with('success', '¡Gracias! Tu feedback ha sido enviado correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No se pudo enviar el feedback.'], 500);
            }
            return back()->with('error', 'No se pudo enviar el feedback.');
        }
    }

    /**
     * Admin: List all feedbacks.
     */
    public function index()
    {
        $feedbacks = $this->feedbackService->getFeedbacks();
        return view('admin.feedback.index', compact('feedbacks'));
    }

    /**
     * Admin: View feedback detail.
     */
    public function show(Feedback $feedback)
    {
        if ($feedback->status === 'new') {
            $this->feedbackService->markAsRead($feedback);
        }

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Admin: Mark as read.
     */
    public function markAsRead(Feedback $feedback)
    {
        $this->feedbackService->markAsRead($feedback);
        return back()->with('success', 'Feedback marcado como leído.');
    }

    /**
     * Admin: Archive feedback.
     */
    public function archive(Feedback $feedback)
    {
        $this->feedbackService->archive($feedback);
        return redirect()->route('admin.feedback.index')->with('success', 'Feedback archivado.');
    }
}
