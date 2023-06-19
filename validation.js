function validateForm(event) {
    event.preventDefault(); // Prevent form submission
    // Get form field values
    var name = document.getElementById('name').value;
    var firstname = document.getElementById('firstname').value;
    var email = document.getElementById('email').value;
    var file = document.getElementById('file').value;
    var description = document.getElementById('description').value;

    // Perform validation for each field
    if (name.length < 2 || name.length > 255) {
        alert('Please enter a name between 2 and 255 characters.');
        return;
    }

    if (firstname.length < 2 || firstname.length > 255) {
        alert('Please enter a first name between 2 and 255 characters.');
        return;
    }

    if (email.length < 2 || email.length > 255) {
        alert('Please enter a valid email address.');
        return;
    }

    // Validate email format using a regular expression
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // Validate file extension
    if (file && !/\.(jpg|png|gif)$/i.test(file)) {
        alert('Please upload a file with a valid extension (jpg, png, gif).');
        return;
    }

    if (description.length < 2 || description.length > 1000) {
        alert('Please enter a description between 2 and 1000 characters.');
        return;
    }

    // If all validations pass, submit the form
    document.getElementById('contactForm').submit();
}

document.getElementById('contactForm').addEventListener('submit', validateForm);