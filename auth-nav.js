/**
 * auth-nav.js - Dynamic navigation helper for Happy Seoul
 * Automatically updates navigation icons and dropdowns based on user login state
 */

(function () {
    async function initAuthNav() {
        try {
            const response = await fetch("api/user_status.php", {
                headers: { "Accept": "application/json" }
            });
            const data = await response.json();

            if (data && data.isLoggedIn && data.user) {
                // User is logged in
                const username = data.user.username;

                // Update account icon links to point to Account.html
                const accountIcons = document.querySelectorAll('a[href="User.html"], a[href="user.html"]');
                accountIcons.forEach(link => {
                    link.href = "Account.html";
                    link.setAttribute("title", "Signed in as " + username);
                    link.style.position = "relative";
                    
                    // Add small active indicator badge if not present
                    if (!link.querySelector(".online-badge")) {
                        const badge = document.createElement("span");
                        badge.className = "online-badge";
                        badge.style.cssText = "position:absolute;top:2px;right:2px;width:8px;height:8px;background:#40c057;border-radius:50%;border:1px solid #fff;";
                        link.appendChild(badge);
                    }
                });

                // Update dropdown menus to include Log Out
                const dropdowns = document.querySelectorAll(".dropdown-content, #myDropdown");
                dropdowns.forEach(menu => {
                    if (!menu.querySelector(".logout-link")) {
                        const logoutLink = document.createElement("a");
                        logoutLink.className = "logout-link";
                        logoutLink.href = "api/logout.php";
                        logoutLink.style.cssText = "color:#e03131;font-weight:600;";
                        logoutLink.innerHTML = `<i class='bx bx-log-out'></i> Log Out (${username})`;
                        menu.appendChild(logoutLink);
                    }
                });
            }
        } catch (e) {
            // Silently fail if server is not reachable
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initAuthNav);
    } else {
        initAuthNav();
    }
})();

