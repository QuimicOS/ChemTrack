@extends('admin.templateAdmin')

@section('title', 'Manage Quiz')

@section('content')
<div class="content-area" style="max-width: 1000px; margin: auto;">
    <div class="text-center mb-4">
        <h1 class="display-5">Manage Quiz</h1>
        <hr class="my-4">
    </div>

    <!-- Description -->
    <div class="alert alert-info" role="alert">
        Below is the list of available questions for the quiz. You can toggle the questions to make them active or inactive.
    </div>

    <!-- Questions List -->
    <div id="questionList" style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 600px; overflow-y: auto;">
        @foreach ($questions as $question)
        <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" class="form-check-input me-3" id="question{{ $question['id'] }}" 
                   value="{{ $question['id'] }}" 
                   {{ in_array($question['id'], $activeQuestionIds) ? 'checked' : '' }}>
            <label class="form-check-label flex-grow-1" for="question{{ $question['id'] }}">
                <span>{{ $question['id'] }}.</span> {{ $question['text'] }}
            </label>
        </div>
        @endforeach
    </div>

    <!-- Save Button -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" onclick="saveQuizSettings()">Save Quiz Settings</button>
    </div>
</div>

<script>
function saveQuizSettings() {
    const activeQuestions = [];
    document.querySelectorAll('#questionList input:checked').forEach((checkbox) => {
        activeQuestions.push(checkbox.value);
    });

    fetch("{{ route('admin.manageQuiz.save') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ active_questions: activeQuestions })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Quiz settings saved successfully!");
        } else {
            alert("Failed to save quiz settings.");
        }
    });
}

</script>
@endsection