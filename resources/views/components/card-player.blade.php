@props([
    'active' => false,
    'revealed' => false,
    'player' => null,
    'context' => null,
    'image' => null,
    'answerId' => null,
])

@php
    if ($revealed) {
        $cardBorderClass = 'card-player-revealed';
        $headerClass = 'card-player-header-revealed';
    } elseif ($active) {
        $cardBorderClass = 'card-player-active';
        $headerClass = 'card-player-header-active';
    } else {
        $cardBorderClass = 'card-player-inactive';
        $headerClass = 'card-player-header-inactive';
    }
@endphp

<div {{ $answerId ? "id=$answerId" : '' }} class="card text-center {{ $cardBorderClass }} card-player-custom player-card"
    data-player="{{ $player ?? '' }}" data-context="{{ $context ?? '' }}" data-answer-id="{{ $answerId ?? '' }}">
    <div class="card-header {{ $headerClass }} py-1 px-2">
        <small class="fw-bold">
            @if (($active || $revealed) && $player)
                {{ $player }}
            @elseif ($context)
                {{ $context }}
            @endif
        </small>
    </div>
    <img src="{{ $image }}" class="card-img-top card-player-img d-block" alt=" ">
</div>

@once
    @push('scripts')
        <script>
            // Global function to update card state
            function updateCardState(answerId, active, revealed) {
                const card = document.getElementById(answerId);
                if (!card) return;

                // Update classes
                card.classList.remove(
                    'card-player-active',
                    'card-player-inactive',
                    'card-player-revealed'
                );

                const header = card.querySelector('.card-header');
                header.classList.remove(
                    'card-player-header-active',
                    'card-player-header-inactive',
                    'card-player-header-revealed'
                );

                if (revealed) {
                    card.classList.add('card-player-revealed');
                    header.classList.add('card-player-header-revealed');
                } else if (active) {
                    card.classList.add('card-player-active');
                    header.classList.add('card-player-header-active');
                } else {
                    card.classList.add('card-player-inactive');
                    header.classList.add('card-player-header-inactive');
                }

                // Update player name visibility
                const headerText = header.querySelector('small');
                if (revealed || active) {
                    headerText.textContent = card.dataset.player || card.dataset.context;
                } else {
                    headerText.textContent = card.dataset.context;
                }
            }

            // Initialize cards with their initial state
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.player-card').forEach(card => {
                    const active = card.classList.contains('card-player-active');
                    const revealed = card.classList.contains('card-player-revealed');

                    // Store initial state in dataset
                    card.dataset.initialActive = active;
                    card.dataset.initialRevealed = revealed;
                });
            });

        </script>
    @endpush
@endonce
