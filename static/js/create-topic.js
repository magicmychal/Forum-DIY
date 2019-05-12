const contentTextbox = new SimpleMDE({element: $("#content")[0]});
contentTextbox.value("This text will appear in the editor");


/*
onSubmit() is called by reCaptcha. We get a token in return, that needs
to be send to the server to get verified.
 */
$(document).on('click', '#btnSubmit', function (event) {
    event.preventDefault();
    // Check if fields are empty and valid
    // try{
    //     if($('#topic_name').val()=='') throw 'Topic cannot be empty';
    //     if($('#category_id').val()=='') throw 'Choose category';
    //     if(contentTextbox.value()=='') throw 'Write some content';
    // } catch (e) {
    //     console.log(e);
    //     return;
    // }

    const form = $('#new-topic-form').serialize() + '&content=' + contentTextbox.value()
    // Make an ajax call
    $.ajax({
        url: 'api-create-topic',
        type: 'POST',
        data: form,
        dataType: 'json'
    }).always(function (response) {
        /*
        Response should include the status code
        and, if successful, the ID so we can go directly to the page.
        Otherwise, the error code. It should be a little bit more specific
         */
        // console.log('response', response);
        if (response.status == 1) {
            // redirect to the proper page
            displaySuccess();
            window.location.href = "topic?id=" + response.topic;
        } else if(response.status == 0){
            console.log('status 0')
            console.log(response.message)
            displayError(response.message);
        } else{
            displayError('Internal Server error')
        }
    });
})





//function for displaying the error message if the signup is invalid
function displayError(message) {
  
    document.getElementById("err-msg").style.display ="block";
    if(message){
      document.querySelector("#err-msg p").textContent = message;
    }
}

//function for displaying the success message
function displaySuccess() {
    document.getElementById("err-msg").style.display = "none";
    document.getElementById("succ-msg").style.display = "block";
}
