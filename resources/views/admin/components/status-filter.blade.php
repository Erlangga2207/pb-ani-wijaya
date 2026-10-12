<select name="status" class="form-select" style="max-width:180px" onchange="this.form.submit()" aria-label="Status">
    <option value="">Semua status</option>
    <option value="active" @selected(request('status') === 'active')>Aktif</option>
    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
</select>
