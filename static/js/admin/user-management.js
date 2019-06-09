let gUsersInfo;
// Find user
$(document).on('click', '#btn-find-user', function () {
    searchRequest()
});

// on key up
$("#userToFind").on('keyup', function (e) {
    searchRequest()
});

// search request
function searchRequest(){
    // hide errors if they already shown
    hideError()
    const userToFind = $('#userToFind').val();
    // send an AJAX request to get the user's info
    $.ajax({
        url: 'admin-users-api',
        type: 'POST',
        data: {
            'txtSearch': userToFind
        },
        dataType: 'json'
    }).always(function (response) {
        //append information
        if (response.status == 1) {
            // execute function to append
            displayList(response.data)
        } else if (response.status == 0) {
            displayError(response.message)
            removeList()
        } else {
            //displayError('Internal Server Error')
            removeList()
        }
    })
}


// Save user data
$(document).on('click', '#btnSaveUser', function () {
    const iUserId = $('#userId').val()
    const iActive = document.getElementById('activeCheck').checked == true ? 1 : 0;
    const iRole = document.getElementById('moderatorCheck').checked == true ? 5 : 4;
    const token = $('#token').val()
    $.ajax({
        url: 'admin-users-update-api',
        type: 'POST',
        data: {
            'iUserId': iUserId,
            'iActive': iActive,
            'iRole': iRole,
            'token': token
        },
        dataType: 'json'
    }).always(function (response) {
        //append information
        if (response.status == 1) {
            // execute function to append
            displaySuccess()
            disableEdit()
            removeList()
            resetgUsersInfo
        } else if (response.status == 0) {
            displayError(response.message)
        } else {
            displayError('Internal Server Error')
        }
    })
})

// click user to edit.
$(document).on('click', '.user-on-list', function () {
    const userId = $(this).attr('data-userId')
    const userInfo = gUsersInfo.find(user => user.id === userId)
    enableEdit(userInfo)
})

// enable edit fields
function enableEdit(dataToEnable) {
    // bind received values to variables
    const iUserId = dataToEnable['id']
    const sUserName = dataToEnable['username']
    const sEmail = dataToEnable['email']
    const iActive = dataToEnable['active']
    const iRole = dataToEnable['user_role_id']

    // insert values into the input fields
    $('#txtUsername').val(sUserName)
    $('#txtEmail').val(sEmail)
    $('#userId').val(iUserId)
    // activate the checkbox
    $('#activeCheck').prop("disabled", false)
    // if the user active, select it
    if (iActive == 1) {
        $('#activeCheck').prop("checked", true)
    } else {
        $('#activeCheck').prop("checked", false)
    }
    $('#moderatorCheck').prop("disabled", false)
    if (iRole == 5) {
        $('#moderatorCheck').prop("checked", true)
    } else if (iRole == 4) {
        $('#moderatorCheck').prop("checked", false)
    }
    $('#btnSaveUser').prop("disabled", false)
}

function disableEdit() {
    // disable current status
    $('#activeCheck').prop("disabled", true)
    $('#moderatorCheck').prop("disabled", true)
    $('#btnSaveUser').prop("disabled", true)

}

function displayList(users) {
    gUsersInfo = users
    // first, remove whatever is lest
    //$('#usersResult').empty()
    $('#usersResult').children().fadeOut(200).promise().then(function () {
        $('#usersResult').empty();
        // then insert new results
        let fadeInTime = 200
        users.forEach(function (user) {
            let txtRole = '';
            if (user.user_role_id == 5) {
                txtRole = 'Moderator'
            } else if (user.user_role_id == 6) {
                txtRole = 'Administrator'
            }
            $('#usersResult').append('<li class="list-group-item d-flex justify-content-between align-items-center user-on-list" data-userId="' + user.id + '">' +
                '                    ' + user.username +
                '                    <span class="badge badge-primary badge-pill">' + txtRole + '</span>\n' +
                '                </li>').hide().fadeIn(fadeInTime)
            fadeInTime = fadeInTime + 100
        })
    });

    //$('#usersResult').append()
}

function removeList() {
    $('#usersResult').children().fadeOut(200).promise().then(function () {
        $('#usersResult').empty();
    })
}

function resetgUsersInfo() {
    gUsersInfo = ''
}