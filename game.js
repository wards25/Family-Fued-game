document.addEventListener("DOMContentLoaded", function() {
    const revealButton = document.querySelector('.reveal-answers');
    const answers = document.querySelectorAll('.answer');

    revealButton.addEventListener('click', function() {
        answers.forEach((answer, index) => {
            const answerText = answer.querySelector('.answer-text');
            answerText.textContent = "???"; // Placeholder for answer

            // Animation to reveal the answer
            setTimeout(() => {
                answer.style.opacity = 1;
                answer.style.transform = 'translateY(0)';
                // Fetch real answer text from the DOM (PHP dynamically injected this)
                answerText.textContent = answer.dataset.answer; 
            }, index * 500); // Stagger the reveal
        });
    });
});
