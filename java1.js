function toggleForm() {
  const title = document.getElementById('formTitle');
  const action = document.getElementById('formAction');
  const role = document.getElementById('roleSelect');
  const link = document.querySelector('.toggle-link');

  if (action.value === "login") {
    title.innerText = "Registrieren";
    action.value = "register";
    role.style.display = "block";
    link.innerText = "Schon registriert? Zum Login";
  } else {
    title.innerText = "Login";
    action.value = "login";
    role.style.display = "none";
    link.innerText = "Noch kein Konto? Jetzt registrieren";
  }
}