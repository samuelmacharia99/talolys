<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Bank name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $tenant->slug ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Plan</label>
        <select name="plan_id" class="form-select">
            @foreach ($plans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6"><label class="form-label">Admin name</label><input name="admin_name" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Admin email</label><input name="admin_email" type="email" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Admin username</label><input name="admin_username" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Admin password</label><input name="admin_password" type="password" class="form-control" required></div>
</div>
