document.addEventListener("DOMContentLoaded", function () {
    const markBtn = document.getElementById("mark-as-read");
    const numBadge = document.getElementById("num-of-notif");
    const unreadCards = document.querySelectorAll(".notification-card.unread");

    if (markBtn) {
        markBtn.addEventListener("click", function () {
            unreadCards.forEach(card => card.classList.remove("unread"));
            if (numBadge) numBadge.textContent = "0";
            window.location.href = "MAR.html";
        });
    }
});

