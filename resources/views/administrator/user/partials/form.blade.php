<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
    <input type="text" name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '') }}"
           placeholder="Masukkan nama lengkap" required>
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label">Email <span class="text-danger">*</span></label>
    <input type="email" name="email"
           class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '') }}"
           placeholder="email@example.com" required>
    @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label">Role <span class="text-danger">*</span></label>
    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
      <option value="">-- Pilih Role --</option>
      @foreach($roles as $role)
        <option value="{{ $role->id_role }}"
          @selected(old('role_id', $user->role_id ?? '') == $role->id_role)>
          {{ $role->name }}
        </option>
      @endforeach
    </select>
    @error('role_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label">Department / Unit</label>
    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
      <option value="">-- Tidak Ada --</option>
      @foreach($departments as $department)
        <option value="{{ $department->id_department }}"
          @selected(old('department_id', $user->department_id ?? '') == $department->id_department)>
          {{ $department->name }}
          @if($department->type)
            ({{ $department->type }})
          @endif
        </option>
      @endforeach
    </select>
    <div class="form-text">Opsional. Wajib diisi untuk role prodi/UPA/dosen.</div>
    @error('department_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label">
      Password
      @if(empty($user))
        <span class="text-danger">*</span>
      @endif
    </label>
    <input type="password" name="password"
           class="form-control @error('password') is-invalid @enderror"
           @if(empty($user)) required @endif
           autocomplete="new-password"
           placeholder="{{ empty($user) ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diubah' }}">
    @error('password')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label">Konfirmasi Password</label>
    <input type="password" name="password_confirmation"
           class="form-control"
           @if(empty($user)) required @endif
           autocomplete="new-password"
           placeholder="Ulangi password">
  </div>
</div>