<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index()
    {
        return view('platform.plans.index', [
            'pageTitle' => 'Plans',
            'plans'     => Plan::orderBy('price')->get(),
        ]);
    }

    public function create()
    {
        return view('platform.plans.form', [
            'pageTitle' => 'Create Plan',
            'plan'      => new Plan(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Plan::create($data);

        $notify[] = ['success', 'Plan created.'];

        return redirect()->route('platform.plans.index')->withNotify($notify);
    }

    public function edit(Plan $plan)
    {
        return view('platform.plans.form', [
            'pageTitle' => 'Edit Plan',
            'plan'      => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validated($request, $plan));

        $notify[] = ['success', 'Plan updated.'];

        return redirect()->route('platform.plans.index')->withNotify($notify);
    }

    protected function validated(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'name'            => 'required|string|max:120',
            'slug'            => ['required', 'string', 'max:60', Rule::unique('plans', 'slug')->ignore($plan?->id)],
            'price'           => 'required|numeric|min:0',
            'stripe_price_id' => 'nullable|string|max:120',
            'max_users'       => 'required|integer|min:1',
            'max_branches'    => 'required|integer|min:1',
            'is_active'       => 'nullable|boolean',
        ]);

        $data['slug'] = Str::lower($data['slug']);
        $data['is_active'] = $request->boolean('is_active');
        $data['enabled_modules'] = $this->defaultModules();

        return $data;
    }

    protected function defaultModules(): object
    {
        return (object) [
            'deposit' => 1,
            'withdraw' => 1,
            'loan' => 1,
            'fdr' => 1,
            'dps' => 1,
        ];
    }
}
