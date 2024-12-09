@extends('admin.templateAdmin')

@section('title', 'Manage Quiz')

@section('content')
    <div class="content-area" style="max-width: 1000px; margin: auto; padding-top: 80px;">
        <div class="text-center mb-4">
            <!-- Title -->
            <h1 class="display-5">Manage Quiz</h1>
            <hr class="my-4">
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Description -->
        <div class="alert alert-info" role="alert">
            Below is the list of available questions for the quiz. Use the toggles to mark questions as active or inactive.
        </div>

        <!-- Questions List -->
        <form method="POST" action="{{ route('admin.manageQuiz.save') }}">
            @csrf
            <div id="questionList"
                style="background-color: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-height: 600px; overflow-y: auto;">
                @foreach ($questions as $index => $question)
                    <div class="d-flex align-items-center mb-3">
                        <!-- Checkbox -->
                        <div class="form-check me-3">
                            <input type="checkbox" class="form-check-input" name="questions[{{ $index }}]"
                                id="question{{ $index }}" {{ $question['status'] === 'active' ? 'checked' : '' }}>
                        </div>
                        <!-- Question Text -->
                        <div>
                            <strong>{{ $index + 1 }}.</strong> {{ $question['text'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Save Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Save Quiz Settings</button>
            </div>
        </form>
    </div>

@section('scripts')
    <script>
        //Function to save and change status (active/inactive) in the text file
        public
        function save(Request $request) {
            $filePath = storage_path('app/private/questions.txt');
            $questions = [];

            if (file_exists($filePath)) {
                $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                foreach($lines as $index => $line) {
                    $parts = explode('|', $line);

                    if (count($parts) >= 6) {
                        // Check if this question was marked as active
                        $status = in_array($index + 1, array_keys($request - > input('questions', []))) ? 'active' :
                            'inactive';
                        $parts[6] = $status;
                        $questions[] = implode('|', $parts);
                    }
                }

                // Save updated questions to the file
                file_put_contents($filePath, implode("\n", $questions));
            }

            return redirect() - > route('admin.manageQuiz.show') - > with('success', 'Quiz settings updated successfully.');
        }
    </script>
@endsection
