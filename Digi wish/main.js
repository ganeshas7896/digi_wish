/* ==================================================================
   main.js — shared interactive behaviour for Digi Wish
   ------------------------------------------------------------------
   Recommendation: keep this file the same across pages; each block
   below checks whether the element it needs actually exists on the
   current page before doing anything, so it's safe to load on
   index.html, signup.html AND dashboard.php.
   ================================================================== */

document.addEventListener("DOMContentLoaded", function () {
  /* ----------------------------------------------------------------
     0. SHOW ?error= / ?success= MESSAGES FROM PHP REDIRECTS
    login.php and signup.php redirect back with a query
     string like index.html?error=invalid_credentials — this reads
     that and shows it in the .form-message box.
  ---------------------------------------------------------------- */
  const params = new URLSearchParams(window.location.search);
  const loginMsg = document.getElementById("loginMessage");
  const signupMsg = document.getElementById("signupMessage");
  const dashMsg = document.getElementById("detailsMessage");

  const MESSAGES = {
    invalid_credentials: "Username, gmail or password is incorrect.",
    missing_fields: "Please fill in every field.",
    account_created: "Account created! You can log in now.",
    username_taken: "That username is already taken.",
    email_taken: "An account with that gmail already exists.",
    password_mismatch: "Passwords do not match.",
    db_error: "Something went wrong on our end. Please try again.",
    order_placed: "Your wish order has been placed! We'll be in touch.",
    order_completed:
      "This order has been closed and moved to the completed tab.",
  };

  function showMessage(box, key, type) {
    if (!box || !key) return;
    box.textContent = MESSAGES[key] || key;
    box.className = "form-message " + type;
    box.hidden = false;
  }

  if (loginMsg) {
    if (params.get("error"))
      showMessage(loginMsg, params.get("error"), "error");
    if (params.get("success"))
      showMessage(loginMsg, params.get("success"), "success");
  }
  if (signupMsg) {
    if (params.get("error"))
      showMessage(signupMsg, params.get("error"), "error");
    if (params.get("success"))
      showMessage(signupMsg, params.get("success"), "success");
  }
  if (dashMsg) {
    if (params.get("error")) showMessage(dashMsg, params.get("error"), "error");
    if (params.get("success"))
      showMessage(dashMsg, params.get("success"), "success");
  }

  /* ----------------------------------------------------------------
     1. SIGN UP FORM — confirm password matches before submitting
  ---------------------------------------------------------------- */
  const signupForm = document.getElementById("signupForm");
  if (signupForm) {
    signupForm.addEventListener("submit", function (e) {
      const password = document.getElementById("password").value;
      const confirm = document.getElementById("confirmPassword").value;
      if (password !== confirm) {
        e.preventDefault();
        showMessage(signupMsg, "password_mismatch", "error");
      }
    });
  }

  /* ----------------------------------------------------------------
     2. GOOGLE SIGN-IN PLACEHOLDER (index.html)
     Recommendation: wire this up to Google Identity Services when
     you're ready — see README.md "Optional: Google Sign-In".
  ---------------------------------------------------------------- */
  const googleBtn = document.getElementById("googleBtn");
  if (googleBtn) {
    googleBtn.addEventListener("click", function () {
      alert(
        "Google Sign-In placeholder.\nSee README.md to connect real Google OAuth.",
      );
    });
  }

  /* ----------------------------------------------------------------
     3. FESTIVAL / OCCASION SELECTION (dashboard.php)
  ---------------------------------------------------------------- */
  const festivalGrid = document.getElementById("festivalGrid");
  const festivalInput = document.getElementById("festivalInput");
  const festivalLabel = document.getElementById("festivalSelectedLabel");
  const wishTypeField = document.getElementById("wishType");

  if (festivalGrid) {
    festivalGrid.addEventListener("click", function (e) {
      const btn = e.target.closest(".festival-btn");
      if (!btn) return;

      festivalGrid
        .querySelectorAll(".festival-btn")
        .forEach((b) => b.classList.remove("selected"));
      btn.classList.add("selected");

      const chosen = btn.dataset.festival;
      festivalInput.value = chosen;
      festivalLabel.textContent = chosen;

      // Convenience: pre-fill the "Wish type" field if it's empty.
      if (wishTypeField && !wishTypeField.value) {
        wishTypeField.value = chosen + " Wish";
      }
    });
  }

  /* ----------------------------------------------------------------
     4. DETAILS BUTTON — show / hide the order form (dashboard.php)
  ---------------------------------------------------------------- */
  const detailsToggleBtn = document.getElementById("detailsToggleBtn");
  const orderForm = document.getElementById("orderForm");
  const orderDateField = document.getElementById("orderDate");

  if (detailsToggleBtn && orderForm) {
    detailsToggleBtn.addEventListener("click", function () {
      const isHidden = orderForm.hasAttribute("hidden");
      if (isHidden) {
        orderForm.removeAttribute("hidden");
        detailsToggleBtn.textContent = "📝 Hide Order Details";
        // Default order date to today, only the first time it's opened.
        if (orderDateField && !orderDateField.value) {
          orderDateField.value = new Date().toISOString().split("T")[0];
        }
        orderForm.scrollIntoView({ behavior: "smooth", block: "start" });
      } else {
        orderForm.setAttribute("hidden", "");
        detailsToggleBtn.textContent = "📝 Order Details";
      }
    });
  }

  /* ----------------------------------------------------------------
     5. ORDER FORM — simple client-side sanity check
  ---------------------------------------------------------------- */
  const detailsMsg = document.getElementById("detailsMessage");
  if (orderForm) {
    orderForm.addEventListener("submit", function (e) {
      const deliveryDate = document.getElementById("deliveryDate");
      const orderDate = document.getElementById("orderDate");
      if (
        deliveryDate &&
        orderDate &&
        deliveryDate.value &&
        orderDate.value &&
        deliveryDate.value < orderDate.value
      ) {
        e.preventDefault();
        detailsMsg.textContent =
          "Delivery date cannot be before the order date.";
        detailsMsg.className = "form-message error";
        detailsMsg.hidden = false;
      }
    });
  }

  /* ----------------------------------------------------------------
     6. ORDER TABS — switch between open and closed orders
  ---------------------------------------------------------------- */
  const orderTabs = document.querySelectorAll(".order-tab");
  const orderPanels = document.querySelectorAll(".order-panel");

  if (orderTabs.length) {
    orderTabs.forEach((tab) => {
      tab.addEventListener("click", function () {
        const requestedTab = tab.dataset.tab;

        orderTabs.forEach((item) => {
          const isActive = item === tab;
          item.classList.toggle("active", isActive);
          item.setAttribute("aria-selected", String(isActive));
        });

        orderPanels.forEach((panel) => {
          const isVisible = panel.id === requestedTab;
          panel.classList.toggle("active", isVisible);
          panel.hidden = !isVisible;
        });
      });
    });
  }
});
