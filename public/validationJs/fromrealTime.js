 document.addEventListener('DOMContentLoaded', function() {
        var nomInput = document.getElementById('user_nom');
        var prenomInput = document.getElementById('user_prenom');
        var emailInput = document.getElementById('user_email');
        var numtelInput = document.getElementById('user_numtel');
        var passwordInput = document.getElementById('user_password');
        var form = document.getElementById('user_form');

        // Ajoutez des écouteurs d'événements sur les champs du formulaire pour la validation en temps réel
        nomInput.addEventListener('input', function() {
            validateNom();
        });

        prenomInput.addEventListener('input', function() {
            validatePrenom();
        });

        emailInput.addEventListener('input', function() {
            validateEmail();
        });

        numtelInput.addEventListener('input', function() {
            validateNumtel();
        });

        passwordInput.addEventListener('input', function() {
            validatePassword();
        });

        form.addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault(); // Empêche la soumission du formulaire si la validation échoue
            }
        });

        // Fonctions de validation pour chaque champ
        function validateNom() {
            var nomValue = nomInput.value.trim();
            if (nomValue === '') {
                showError(nomInput, 'Merci de saisir votre nom.');
                return false; // Validation échoue
            } else if (nomValue.length < 3) {
                showError(nomInput, 'Le nom doit avoir au moins 3 caractères.');
                return false; // Validation échoue
            } else {
                hideError(nomInput);
                return true; // Validation réussie
            }
        }

        function validatePrenom() {
            var prenomValue = prenomInput.value.trim();
            if (prenomValue === '') {
                showError(prenomInput, 'Merci de saisir votre prénom.');
                return false; // Validation échoue
            } else if (prenomValue.length < 3) {
                showError(prenomInput, 'Le prénom doit avoir au moins 3 caractères.');
                return false; // Validation échoue
            } else {
                hideError(prenomInput);
                return true; // Validation réussie
            }
        }

        function validateEmail() {
            var emailValue = emailInput.value.trim();
            if (emailValue === '') {
                showError(emailInput, "L'email est obligatoire.");
                return false; // Validation échoue
            } else if (!isValidEmail(emailValue)) {
                showError(emailInput, "L'email n'est pas valide.");
                return false; // Validation échoue
            } else {
                hideError(emailInput);
                return true; // Validation réussie
            }
        }

        function validateNumtel() {
            var numtelValue = numtelInput.value.trim();
            if (numtelValue === '') {
                showError(numtelInput, 'Merci de saisir votre numéro de téléphone.');
                return false; // Validation échoue
            } else if (!isValidPhoneNumber(numtelValue)) {
                showError(numtelInput, 'Entrez un numéro de téléphone valide.');
                return false; // Validation échoue
            } else {
                hideError(numtelInput);
                return true; // Validation réussie
            }
        }

        function validatePassword() {
            var passwordValue = passwordInput.value.trim();
            if (passwordValue === '') {
                showError(passwordInput, "Le mot de passe est obligatoire.");
                return false; // Validation échoue
            } else if (!isValidPassword(passwordValue)) {
                showError(passwordInput, 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
                return false; // Validation échoue
            } else {
                hideError(passwordInput);
                return true; // Validation réussie
            }
        }

        // Fonction pour afficher les erreurs en rouge
        function showError(input, message) {
            var errorDiv = input.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                input.parentNode.insertBefore(errorDiv, input.nextElementSibling);
            }
            errorDiv.textContent = message;
            errorDiv.style.color = 'red'; // Couleur rouge pour les messages d'erreur
        }

        // Fonction pour masquer les erreurs
        function hideError(input) {
            var errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('error-message')) {
                errorDiv.textContent = '';
            }
        }

        // Fonction pour valider l'email
        function isValidEmail(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // Fonction pour valider le numéro de téléphone
        function isValidPhoneNumber(numtel) {
            var regex = /^(\+216)?[2459]\d{7}$/;
            return regex.test(numtel);
        }

        // Fonction pour valider le mot de passe
        function isValidPassword(password) {
            var regex = /^(?=.\d)(?=.[a-z])(?=.[A-Z])(?=.[^\w\d\s:])([^\s]){8,}$/;
            return regex.test(password);
        }

        // Fonction de validation du formulaire
        function validateForm() {
            // Valider chaque champ du formulaire
            var nomValid = validateNom();
            var prenomValid = validatePrenom();
            var emailValid = validateEmail();
            var numtelValid = validateNumtel();
            var passwordValid = validatePassword();

            // Retourner true si tous les champs sont valides, sinon retourner false
            return nomValid && prenomValid && emailValid && numtelValid && passwordValid;
        }});




