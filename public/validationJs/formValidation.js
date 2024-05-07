
/////////////////////////// sweet alert //////////////////////////
function deleteCoach(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        customClass: {
            confirmButton: 'btn btn-primary ml-1',
            cancelButton: 'btn btn-outline-danger ml-3', // Add ml-3 for spacing
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}

function deleteMembre(url) {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Vous ne pourrez pas annuler cela!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Non, annuler!',
        customClass: {
            confirmButton: 'btn btn-primary ml-1',
            cancelButton: 'btn btn-outline-danger ml-3', // Add ml-3 for spacing
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}


function success() {
    Swal.fire({
        title: 'Good job!',
        text: 'Votre formulaire a été soumis avec succès!',
        icon: 'success',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    })
}


function error() {
    Swal.fire({
        title: 'Erreur!',
        text: ' Veuillez vérifier vos entrées et réessayer!',
        icon: 'error',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    })
}

function successuser() {
    Swal.fire({
        title: 'Bravo!',
        text: 'Le compte a été créé avec succès! Un email contenant le mot de passe a été envoyé à l\'utilisateur.',
        icon: 'success',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    })
}

function erroruser() {
    Swal.fire({
        title: 'Erreur!',
        text: ' User already exist! Veuillez vérifier vos entrées et réessaye.',
        icon: 'error',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    })
}

function topEnd() {
    this.$swal({
        position: 'top-end',
        icon: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    })
}

/////////////////////////// form edit password validation //////////////////////////
document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        if (!isValidNormalForm()) {
            e.preventDefault();
            error();
        }
    });
});

function isValidPassword() {
    var password = document.getElementById('userpassword').value;
    var errorElement = document.getElementById('error_userpassword');
    var successElement = document.getElementById('success_userpassword');

    if (password.length < 8) {
        errorElement.textContent = 'Le mot de passe doit comporter au moins 8 caractères.';
        successElement.textContent = '';
        return false;
    } else {
        errorElement.textContent = '';
        successElement.textContent = 'Le mot de passe est valide.';
        return true;
    }
}

function isValidPasswordConfirm() {
    var password = document.getElementById('userpassword').value;
    var passwordConfirm = document.getElementById('userpasswordConfirm').value;
    var errorElement = document.getElementById('error_userpasswordConfirm');
    var successElement = document.getElementById('success_userpasswordConfirm');

    if (password !== passwordConfirm) {
        errorElement.textContent = 'Les mots de passe ne correspondent pas.';
        successElement.textContent = '';
        return false;
    } else {
        errorElement.textContent = '';
        successElement.textContent = 'Les mots de passe correspondent.';
        return true;
    }
}

function isValidChangepwdForm() {
    if (isValidPassword() && isValidPasswordConfirm()) {
        // If both password and confirmed password are valid
        success(); // Display success message
        return true;
    } else {
        // If either password or confirmed password is invalid
        error(); // Display error message
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('editPasswordForm');
    form.addEventListener('submit', function(e) {
        if (!isValidChangepwdForm()) {
            e.preventDefault(); // Prevent form submission if the form is not valid
        }
    });
});


/////////////////////////// form validation normal  //////////////////////////
document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        if (!isValidNormalForm()) {
            e.preventDefault();
            error();
        }
    });
});

function isValidEmail() {
    var email = document.getElementById("email").value;
    var atposition = email.indexOf("@");
    var dotposition = email.lastIndexOf(".");
    if (atposition < 1 || dotposition < atposition + 2 || dotposition + 2 >= email.length) {
        document.getElementById("error_email").innerHTML = "• Merci de saisir un  email valide.";
        document.getElementById("success_email").innerHTML = "";
        document.getElementById("email").style.border = "1px solid red";
        return false;
    }
    else {
        document.getElementById("error_email").innerHTML = "";
        document.getElementById("success_email").innerHTML = "• Email valide.";
        document.getElementById("email").style.border = "1px solid green";
        return true;
    }
}

function isValidName() {
    var name = document.getElementById("name").value;
    if (name.length < 3) {
        document.getElementById("error_name").innerHTML = "• Le nom doit avoir au moins 3 caractères.";
        document.getElementById("success_name").innerHTML = "";
        document.getElementById("name").style.border = "1px solid red";
        return false;
    }
    else {
        document.getElementById("error_name").innerHTML = "";
        document.getElementById("success_name").innerHTML = "• Nom valide.";
        document.getElementById("name").style.border = "1px solid green";
        return true;
    }
}

        function isValidPhoneNumber() {
            var telephone = document.getElementById("telephone").value;
            if (!/^\d{8}$/.test(telephone)) {
                document.getElementById("error_Telephone").innerHTML = "• Entrez un numéro de téléphone valide.";
                document.getElementById("success_Telephone").innerHTML = "";
                document.getElementById("telephone").style.border = "1px solid red";
                return false;
            }
            else {
                document.getElementById("error_Telephone").innerHTML = "";
                document.getElementById("success_Telephone").innerHTML = "• Numéro de téléphone valide.";
                document.getElementById("success_email").innerHTML = "";
                document.getElementById("telephone").style.border = "1px solid green";
                return true;
            } 
        }

           function isValidNormalForm() {
            if (isValidName() && isValidEmail() && isValidPhoneNumber()) {
                success();
                return true;
            }
            else {
                error();
                return false;
            }
        }


/////////////////////////// form validation add coach & check user email already exist //////////////////////////

       
        function checkUser(callback) {
            username = document.getElementById("email").value;
            console.log(username);

            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure the request
            var url = document.body.dataset.checkUserUrl;
             xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
            // Set up a callback function to handle the response
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    
                    if (xhr.status === 200) {
                        console.log("Im here")
                        // Response received successfully
                        var response = xhr.responseText;
                        console.log('Response:', response);
                        if (response === 'true') {
                            // User exists
                            
                            // console.log('User exists');
        callback(true);
                        } else if(response === 'false') {

                            // User does not exist
                            // console.log('User does not exist');
                           callback(false);
                        }
                        else {
                            console.log("not true not false")
                        }
                    } else {
                        // Error occurred
                        console.error('Error: ' + xhr.status);
                    }
                }
            };
        
            // Send the request with the username as data
            xhr.send('username=' + username);
        }
        
    
        function isValidForm( callback) {
            if (isValidName() && isValidEmail() && isValidPhoneNumber()) {
                checkUser(function(userExists) {
                    if (!userExists) {
                        
                        // User does not exist
                        successuser();
                        callback(true);
                        //document.getElementById("erreur").style.display = "inline";
                        // Soumettre le formulaire
                       
                    } else {
                       
                        // User exists
                        erroruser();
                        document.getElementById("erreur").innerHTML = "user already exist!";
                        callback(false);
                        
                    }
                });
            } else {
                // Form validation failed
               // alert("Please fill in all fields correctly");
                return false;
            }
        }

        /////////////////////////// form prevent submit add coach //////////////////////////

        document.addEventListener('DOMContentLoaded', (event) => {
            document.getElementById('addCoachForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent form submission
        
                isValidForm(function(isFormValid) {
                    if (isFormValid) {
                        // Form is valid, manually submit the form
                        e.target.submit();
                    } else {
                        console.log("false");
                        //error();
                    }
                });
            });
        });


        