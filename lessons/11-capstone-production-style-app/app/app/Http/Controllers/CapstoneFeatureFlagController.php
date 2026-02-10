<?php

namespace App\Http\Controllers;

use App\Support\CapstoneFeatures;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Pennant\Feature;

class CapstoneFeatureFlagController extends Controller
{
    public function dashboard(): View
    {
        return view('capstone.dashboard', [
            'flags' => $this->flagPayload(),
        ]);
    }

    public function updateWeb(Request $request, string $feature): RedirectResponse
    {
        if (! CapstoneFeatures::isValid($feature)) {
            abort(404);
        }

        $enabled = $request->boolean('enabled');

        $this->setFeatureState($feature, $enabled);

        return redirect()
            ->route('capstone.dashboard')
            ->with('status', sprintf('Feature [%s] is now %s.', $feature, $enabled ? 'enabled' : 'disabled'));
    }

    public function indexApi(): JsonResponse
    {
        $flags = $this->flagPayload();

        return response()->json([
            'data' => $flags,
            'meta' => [
                'count' => count($flags),
            ],
        ]);
    }

    public function updateApi(Request $request, string $feature): JsonResponse
    {
        if (! CapstoneFeatures::isValid($feature)) {
            return response()->json([
                'error' => [
                    'code' => 'feature_not_found',
                    'message' => sprintf('Unknown feature [%s].', $feature),
                    'details' => null,
                ],
            ], 404);
        }

        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $enabled = (bool) $validated['enabled'];

        $this->setFeatureState($feature, $enabled);

        return response()->json([
            'data' => [
                'feature' => $feature,
                'enabled' => Feature::active($feature),
            ],
            'meta' => [
                'updated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * @return list<array{feature: string, label: string, enabled: bool}>
     */
    private function flagPayload(): array
    {
        $labels = CapstoneFeatures::labels();
        $payload = [];

        foreach (CapstoneFeatures::all() as $feature) {
            $payload[] = [
                'feature' => $feature,
                'label' => $labels[$feature],
                'enabled' => Feature::active($feature),
            ];
        }

        return $payload;
    }

    private function setFeatureState(string $feature, bool $enabled): void
    {
        if ($enabled) {
            Feature::activate($feature);

            return;
        }

        Feature::deactivate($feature);
    }
}
