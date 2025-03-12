document.addEventListener("DOMContentLoaded", () => {
    const themeToggle = document.getElementById("theme-toggle");
    const htmlElement = document.documentElement;
    const sunIcon = document.getElementById("theme-toggle-light");
    const moonIcon = document.getElementById("theme-toggle-dark");

    function updateIcons() {
        if (htmlElement.classList.contains("dark")) {
            sunIcon.classList.remove("hidden");
            moonIcon.classList.add("hidden");
        } else {
            sunIcon.classList.add("hidden");
            moonIcon.classList.remove("hidden");
        }
    }

    if (localStorage.getItem("theme") === "dark") {
        htmlElement.classList.add("dark");
    }
    updateIcons();

    themeToggle.addEventListener("click", () => {
        htmlElement.classList.toggle("dark");
        localStorage.setItem("theme", htmlElement.classList.contains("dark") ? "dark" : "light");
        updateIcons();
    });
});