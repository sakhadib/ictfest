<label>
    <span class="{{ $labelClass }}">Name</span>
    <input name="name" value="{{ old('name', $person?->name) }}" required class="{{ $inputClass }}" placeholder="Full name">
</label>

<label>
    <span class="{{ $labelClass }}">Student ID</span>
    <input name="student_id" value="{{ old('student_id', $person?->student_id) }}" class="{{ $inputClass }}" placeholder="Student ID">
</label>

<label>
    <span class="{{ $labelClass }}">Phone</span>
    <input name="phone" value="{{ old('phone', $person?->phone) }}" class="{{ $inputClass }}" placeholder="01XXXXXXXXX">
</label>

<label>
    <span class="{{ $labelClass }}">Team</span>
    <input name="team" value="{{ old('team', $person?->team) }}" class="{{ $inputClass }}" placeholder="Operations team">
</label>

<label>
    <span class="{{ $labelClass }}">Status</span>
    <select name="status" class="{{ $inputClass }}">
        @foreach($statuses as $status)
            <option value="{{ $status }}" @selected(old('status', $person?->status ?? 'other') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</label>

<label>
    <span class="{{ $labelClass }}">Comments</span>
    <textarea name="comments" rows="3" class="{{ $inputClass }}" placeholder="Optional notes">{{ old('comments', $person?->comments) }}</textarea>
</label>
