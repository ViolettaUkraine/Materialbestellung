function toggleForm() {
  const title = document.getElementById('formTitle');
  const action = document.getElementById('formAction');
  const registrationFields = document.getElementById('registrationFields');
  const link = document.querySelector('.toggle-link');

  if (action.value === "login") {
    title.innerText = "Registrieren";
    action.value = "register";
    registrationFields.style.display = "block";
    link.innerText = "Schon registriert? Zum Login";
  } else {
    title.innerText = "Login";
    action.value = "login";
    registrationFields.style.display = "none";
    link.innerText = "Noch kein Konto? Jetzt registrieren";
  }
}