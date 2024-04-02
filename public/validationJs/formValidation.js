
// function isValidEmail() 
//         {
//             if(document.getElementById("email").value.length < 6){
//             document.getElementById("error_email").innerHTML = "Please enter a valid email format";
//             }
//         }



function isValidEmail() {
    var email = document.getElementById("email").value;
    var atposition = email.indexOf("@");
    var dotposition = email.lastIndexOf(".");
    if (atposition < 1 || dotposition < atposition + 2 || dotposition + 2 >= email.length) {
        document.getElementById("error_email").innerHTML = "• Merci de saisir un  email valide.";
        document.getElementById("success_email").innerHTML = "";
        return false;
    }
    else {
        document.getElementById("error_email").innerHTML = "";
        document.getElementById("success_email").innerHTML = "• Email valide.";
        return true;
    }
}

function isValidName() {
    var name = document.getElementById("name").value;
    if (name.length < 3) {
        document.getElementById("error_name").innerHTML = "• Le nom doit avoir au moins 3 caractères.";
        document.getElementById("success_name").innerHTML = "";
        return false;
    }
    else {
        document.getElementById("error_name").innerHTML = "";
        document.getElementById("success_name").innerHTML = "• Nom valide.";
        return true;
    }
}

        // function isValidPhoneNumber()   
        // {
        //     if (!/^\d{8}$/.test(document.getElementById("telephone").value)) {
        //         document.getElementById("error_Telephone").innerHTML = "Please enter a valid telephone number";
                
        //     }
        // }

        function isValidPhoneNumber() {
            var telephone = document.getElementById("telephone").value;
            if (!/^\d{8}$/.test(telephone)) {
                document.getElementById("error_Telephone").innerHTML = "• Entrez un numéro de téléphone valide.";
                document.getElementById("success_Telephone").innerHTML = "";
            }
            else {
                document.getElementById("error_Telephone").innerHTML = "";
                document.getElementById("success_Telephone").innerHTML = "• Numéro de téléphone valide.";
            } 
        }

         
           