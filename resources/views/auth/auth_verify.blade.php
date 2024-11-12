@extends('layouts.app')

@section('content')
<div class="container">
    <div class="content-header row">
        <div class="col-md-12">
            <x-flash-message></x-flash-message>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Two-Factor Authentication') }}</div>

                <div class="card-body">
                    <form id="verificationForm" method="POST" action="{{ route('verifyToken') }}">
                        @csrf
                        <label for="token">Enter the 6-digit code sent to your email:</label>
                        <div class="input-group">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" name="token[]" maxlength="1" data-index="{{ $i }}" required>
                                @endfor
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" id="submitBtn">
                            {{ __('Verify') }}
                        </button>
                    </form>
                    <p class="mt-3">Time left: <span id="timer"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .input-group {
        display: flex;
        justify-content: space-between;
        max-width: 300px;
        margin: 0 auto;
    }

    .input-group input {
        width: 40px;
        height: 40px;
        text-align: center;
        font-size: 1.5rem;
        border: 2px solid #ddd;
        border-radius: 4px;
        margin: 0 5px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
        transition: border-color 0.3s;
    }

    .input-group input:focus {
        border-color: #007bff;
        outline: none;
    }

    .input-group input.error {
        border-color: #dc3545;
    }

    button {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>
@endsection

@section('jquery')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.input-group input');
        const timerElement = document.getElementById('timer');
        const submitBtn = document.getElementById('submitBtn');
        let timeLeft = {{$remainingTime}};

        // Countdown timer
        const countdown = setInterval(() => {
            timeLeft--;

            // Calculate minutes and seconds
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            // Format the time as MM:SS
            const timeFormatted = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            timerElement.textContent = timeFormatted;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                submitBtn.disabled = true; // Disable the submit button when time is up
                timerElement.textContent = "Expired"; // Show "Expired" after time is up
            }
        }, 1000);

        // Handle input focus and verification
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                if (this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Form submit validation
        document.getElementById('verificationForm').addEventListener('submit', function(event) {
            const values = Array.from(inputs).map(input => input.value).join('');
            if (values.length !== 6) {
                event.preventDefault();
                inputs.forEach(input => {
                    if (input.value === '') {
                        input.classList.add('error');
                    }
                });
            }
        });
    });
</script>
@endsection