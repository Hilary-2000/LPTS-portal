function cObj(params) {
    return document.getElementById(params);
}

function valObj(params) {
    return document.getElementById(params).value;
}

function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function checkBlank(id) {
    let err = 0;
    if (cObj(id).value.trim().length > 0) {
        if (cObj(id).value.trim() == 'N/A') {
            redBorder(cObj(id));
            err++;
        } else {
            grayBorder(cObj(id));
        }
    } else {
        redBorder(cObj(id));
        err++;
    }
    return err;
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]'
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}
function sendDataPost(method, file, datapassing, object1, object2) {
    //make the loading window show
    // datapassing = escape(datapassing);
    cObj(object2.id).classList.remove("hide");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            cObj(object2.id).classList.add("hide");
        } else if (this.status == 500) {
            cObj(object2.id).classList.add("hide");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        }
    };
    xml.open(method, "" + file, true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send(datapassing);
}

window.onload = function () {
    // set the student message initiator listeners
    var send_message_student = document.getElementsByClassName("send_message_student");
    for (let index = 0; index < send_message_student.length; index++) {
        const element = send_message_student[index];
        element.addEventListener("click",sendMessageDetails);
    }

    // student chats
    var student_chats = document.getElementsByClassName("student_chats");
    for (let index = 0; index < student_chats.length; index++) {
        const element = student_chats[index];
        element.addEventListener("click",checkChats);
    }
    
    // after a 30 seconds get new messages
    setInterval(() => {
        // get the messages
        getNewMessages();
    }, 5000);
}

var active_button = null;

function getNewMessages() {
    if (true) {
        const xhr = new XMLHttpRequest();
        // cObj("chat_loaders").classList.remove("d-none");
    
        xhr.open('GET', "/Teacher/DiscussionForum?get_teacher_chats=true");
        // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.addEventListener('load', function (e) {
            
            setTimeout(() => {
                // cObj("chat_loaders").classList.add("d-none");
            }, 10);
            
            var response = this.response;
            if (hasJsonStructure(response)) {
                response = JSON.parse(response);
                console.log(response.student_chats);
                var data_to_display = "";
                if (response.student_chats.length) {
                    for (let index = 0; index < response.student_chats.length; index++) {
                        const element = response.student_chats[index];
                        data_to_display+="<li  class='student_chats "+(active_button == element.student_detail.adm_no ? 'active' : '')+"' id='student_chats_"+element.student_detail.adm_no+"'>";
                        data_to_display+="<input type='hidden' id='chat_student_details_"+element.student_detail.adm_no+"' value='"+JSON.stringify(element.student_detail)+"'>";
                        data_to_display+="<a href='javascript: void(0);'>";
                        data_to_display+="<div class='d-flex'>";
                        data_to_display+="<div class='flex-shrink-0 align-self-center me-3'>";
                        data_to_display+="<i class='mdi mdi-circle font-size-10'></i>";
                        data_to_display+="</div>";
                        data_to_display+="<div class='avatar-xs align-self-center me-3'>";
                        data_to_display+="<span class='avatar-title rounded-circle bg-primary-subtle text-primary'>";
                        data_to_display+=element.student_detail.name.substr(0,1);
                        data_to_display+="</span>";
                        data_to_display+="</div>";    
                        data_to_display+="<div class='flex-grow-1 overflow-hidden'>";
                        data_to_display+="<h5 class='text-truncate font-size-14 mb-1'>"+element.student_detail.name+"</h5>";
                        data_to_display+="<p class='text-truncate mb-0'>"+(element.last_chat.chat_content.length > 35 ? element.last_chat.chat_content.substr(0,35)+"..." : element.last_chat.chat_content)+"</p>";
                        data_to_display+="</div>";
                        data_to_display+="<div class='font-size-11'>"+timeAgoFromNow(element.last_chat.date_sent)+"</div>";
                        data_to_display+="</div>";
                        data_to_display+="</a>"
                        data_to_display+="</li>";
                    } 
                }else{
                    data_to_display+="<li class='active'>";
                        data_to_display+="<a href='javascript: void(0);'>";
                            data_to_display+="<div class='d-flex'>";
                                data_to_display+="<div class='flex-shrink-0 align-self-center me-3'>"
                                data_to_display+="</div>";
                                data_to_display+="<div class='flex-shrink-0 align-self-center me-3'>";
                                    data_to_display+="<div class='avatar-xs'>";
                                        data_to_display+="<span class='avatar-title rounded-circle bg-primary-subtle text-primary'>"
                                            data_to_display+="N";
                                        data_to_display+="</span>";
                                    data_to_display+="</div>";
                                data_to_display+="</div>";
                                data_to_display+="<div class='flex-grow-1 overflow-hidden'>";
                                    data_to_display+="<h5 class='text-truncate font-size-14 mb-1'>No Student Chats found! <br>Start sending Messages</h5>";
                                data_to_display+="</div>";
                            data_to_display+="</div>";
                        data_to_display+="</a>";
                    data_to_display+="</li>";
                }
                cObj("all_student_chats").innerHTML = data_to_display;
                // student chats
                var student_chats = document.getElementsByClassName("student_chats");
                for (let index = 0; index < student_chats.length; index++) {
                    const element = student_chats[index];
                    element.addEventListener("click",checkChats);
                }
            }
        });
        xhr.addEventListener('error', function (e) {
            console.log('An error occurred.');
        });
    
        const formData = new FormData();
        formData.append('get_teacher_chats', "true");
        xhr.send(formData);
    }
}

function timeAgoFromNow(dateString) {
    // Parse the input date string
    const year = parseInt(dateString.substring(0, 4), 10);
    const month = parseInt(dateString.substring(4, 6), 10) - 1; // Months are zero-based
    const day = parseInt(dateString.substring(6, 8), 10);
    const hours = parseInt(dateString.substring(8, 10), 10);
    const minutes = parseInt(dateString.substring(10, 12), 10);
    const seconds = parseInt(dateString.substring(12, 14), 10);

    // Create a Date object for the input date
    const inputDate = new Date(year, month, day, hours, minutes, seconds);

    // Get the current date and time
    const currentDate = new Date();

    // Calculate the difference in milliseconds
    const timeDifference = currentDate - inputDate;

    // Convert milliseconds to seconds
    const secondsDifference = Math.floor(timeDifference / 1000);

    // Define time intervals
    const intervals = {
        year: 31536000,
        month: 2592000,
        day: 86400,
        hour: 3600,
        minute: 60,
    };

    // Calculate the time ago
    for (let interval in intervals) {
        const value = Math.floor(secondsDifference / intervals[interval]);
        if (value >= 1) {
            return `${value} ${interval}${value > 1 ? 's' : ''} ago`;
        }
    }

    return 'Just now';
}

function checkChats() {
    var this_id = this.id.substr(14);
    active_button = this_id;
    
    // get the student details
    var student_details = valObj("chat_student_details_"+this_id);
    if (hasJsonStructure(student_details)) {
        student_details = JSON.parse(student_details);
        // get message
        get_messages(student_details.adm_no);
    }

    // add the current chat as the active one
    var student_chats = document.getElementsByClassName("student_chats");
    for (let index = 0; index < student_chats.length; index++) {
        const element = student_chats[index];
        element.classList.remove("active");
    }

    // add as active
    this.classList.add("active");
}

// function SMS details
function get_messages(student_id) {
    // get the receipient details
    cObj("receipient_id_message").value = student_id;
    var student_details = valObj("student_details_"+student_id);
    
    // get the student details
    if (hasJsonStructure(student_details)) {
        student_details = JSON.parse(student_details);
        cObj("receipient_name").innerHTML = student_details.student_fullname+" - {"+student_details.adm_no+"}";
        cObj("receipient_type_flag").innerHTML = "Student";

        // get the conversations between gloria and this sender
        if (true) {
            const xhr = new XMLHttpRequest();
            cObj("chat_loaders").classList.remove("d-none");
        
            xhr.open('POST', "/Teacher/Get");
            // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.addEventListener('load', function (e) {
                
                setTimeout(() => {
                    cObj("chat_loaders").classList.add("d-none");
                }, 10);
                
                var response = this.response;
                if (hasJsonStructure(response)) {
                    response = JSON.parse(response);
                    var messages = response.messages != null ? response.messages : [];
                    var sender = response.sender != undefined ? response.sender : null;
                    if (messages.length > 0) {
                        // display chats
                        var data_to_display = "";
                        for (let index = 0; index < messages.length; index++) {
                            const element = messages[index];
                            data_to_display+="<li>"; 
                                data_to_display+="<div class='chat-day-title'>";
                                    data_to_display+="<span class='title'>"+element.full_date+"</span>";
                                data_to_display+="</div>";
                            data_to_display+="</li>";
                            for (let index_2 = 0; index_2 < element.messages.length; index_2++) {
                                const elem = element.messages[index_2];
                                if (elem.chat_sender == sender) {
                                    data_to_display+="<li class='right'>";
                                }else{
                                    data_to_display+="<li>";
                                }
                                    data_to_display+="<div class='conversation-list'>";
                                        data_to_display+="<div class='dropdown'>";
                                            data_to_display+="<a class='dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
                                                data_to_display+="<i class='bx bx-dots-vertical-rounded'></i>";
                                            data_to_display+="</a>";
                                            data_to_display+="<div class='dropdown-menu'>";
                                                data_to_display+="<a class='dropdown-item' href='#'>Delete</a>";
                                                // data_to_display+="<a class='dropdown-item' href='#'>Save</a>";
                                            data_to_display+="</div>";
                                        data_to_display+="</div>";
                                        data_to_display+="<div class='ctext-wrap'>";
                                            data_to_display+="<div class='conversation-name'>"+student_details.student_fullname+" - {"+student_details.adm_no+"}"+"</div>";
                                            data_to_display+="<p>";
                                                data_to_display+=""+elem.chat_content+"";
                                            data_to_display+="</p>";
                                            data_to_display+="<p class='chat-time mb-0'><i class='bx bx-time-five align-middle me-1'></i> "+elem.time_sent+"</p>";
                                        data_to_display+="</div>";
                                    data_to_display+="</div>";
                                data_to_display+="</li>";
                            }
                        }
                        cObj("message_contents").innerHTML = data_to_display;
                    }else{
                        // display no chats
                            var data_to_display="<li>"; 
                                    data_to_display+="<div class='chat-day-title'>";
                                        data_to_display+="<span class='title'>No Conversations available!</span>";
                                    data_to_display+="</div>";
                                data_to_display+="</li>";
                        cObj("message_contents").innerHTML = data_to_display;
                    }
                }
            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred.');
            });
        
            const formData = new FormData();
            formData.append('recipient_id', student_details.adm_no);
            xhr.send(formData);
        }
    }
}

// send message details
function sendMessageDetails() {
    // get the receipient details
    var student_id = this.id.substr(21);
    cObj("receipient_id_message").value = student_id;
    var student_details = valObj("student_details_"+student_id);
    
    // get the student details
    if (hasJsonStructure(student_details)) {
        student_details = JSON.parse(student_details);
        cObj("receipient_name").innerHTML = student_details.student_fullname+" - {"+student_details.adm_no+"}";
        cObj("receipient_type_flag").innerHTML = "Student";

        // get the conversations between gloria and this sender
        if (true) {
            const xhr = new XMLHttpRequest();
            cObj("chat_loaders").classList.remove("d-none");
        
            xhr.open('POST', "/Teacher/Get");
            // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.addEventListener('load', function (e) {
                
                setTimeout(() => {
                    cObj("chat_loaders").classList.add("d-none");
                }, 10);
                
                var response = this.response;
                if (hasJsonStructure(response)) {
                    response = JSON.parse(response);
                    var messages = response.messages != null ? response.messages : [];
                    var sender = response.sender != undefined ? response.sender : null;
                    if (messages.length > 0) {
                        // display chats
                        var data_to_display = "";
                        for (let index = 0; index < messages.length; index++) {
                            const element = messages[index];
                            data_to_display+="<li>"; 
                                data_to_display+="<div class='chat-day-title'>";
                                    data_to_display+="<span class='title'>"+element.full_date+"</span>";
                                data_to_display+="</div>";
                            data_to_display+="</li>";
                            for (let index_2 = 0; index_2 < element.messages.length; index_2++) {
                                const elem = element.messages[index_2];
                                if (elem.chat_sender == sender) {
                                    data_to_display+="<li class='right'>";
                                }else{
                                    data_to_display+="<li>";
                                }
                                    data_to_display+="<div class='conversation-list'>";
                                        data_to_display+="<div class='dropdown'>";
                                            data_to_display+="<a class='dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
                                                data_to_display+="<i class='bx bx-dots-vertical-rounded'></i>";
                                            data_to_display+="</a>";
                                            data_to_display+="<div class='dropdown-menu'>";
                                                data_to_display+="<a class='dropdown-item' href='#'>Delete</a>";
                                                // data_to_display+="<a class='dropdown-item' href='#'>Save</a>";
                                            data_to_display+="</div>";
                                        data_to_display+="</div>";
                                        data_to_display+="<div class='ctext-wrap'>";
                                            data_to_display+="<div class='conversation-name'>"+student_details.student_fullname+" - {"+student_details.adm_no+"}"+"</div>";
                                            data_to_display+="<p>";
                                                data_to_display+=""+elem.chat_content+"";
                                            data_to_display+="</p>";
                                            data_to_display+="<p class='chat-time mb-0'><i class='bx bx-time-five align-middle me-1'></i> "+elem.time_sent+"</p>";
                                        data_to_display+="</div>";
                                    data_to_display+="</div>";
                                data_to_display+="</li>";
                            }
                        }
                        cObj("message_contents").innerHTML = data_to_display;
                    }else{
                        // display no chats
                            var data_to_display="<li>"; 
                                    data_to_display+="<div class='chat-day-title'>";
                                        data_to_display+="<span class='title'>No Conversations available!</span>";
                                    data_to_display+="</div>";
                                data_to_display+="</li>";
                        cObj("message_contents").innerHTML = data_to_display;
                    }
                }
            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred.');
            });
        
            const formData = new FormData();
            formData.append('recipient_id', student_details.adm_no);
            xhr.send(formData);
        }
    }
}

cObj("send_message").onclick = function () {
    var message_content = valObj("message_content");
    if(message_content.length > 0){
        var datapass = "message_content="+message_content+"&receipient="+valObj("receipient_id_message");
        const xhr = new XMLHttpRequest();
            cObj("chat_loaders").classList.remove("d-none");
        
            xhr.open('POST', "/Teacher/Send");
            // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.addEventListener('load', function (e) {
                
                setTimeout(() => {
                    cObj("chat_loaders").classList.add("d-none");
                    get_messages(valObj("receipient_id_message"));
                }, 10);
                
                var response = this.response;
                if (hasJsonStructure(response)) {
                    response = JSON.parse(response);
                    if (response.success) {
                        getNewMessages();
                        cObj("error_message_placeholder").innerHTML = "<p class='text-success px-2'>"+response.message+"!</p>";
                    }else{
                        cObj("error_message_placeholder").innerHTML = "<p class='text-danger px-2'>An error occured!</p>";
                    }

                    setTimeout(() => {
                        cObj("error_message_placeholder").innerHTML = "";
                    }, 3000);
                    cObj("message_content").value = "";
                }
            });
            xhr.addEventListener('error', function (e) {
                console.log('An error occurred.');
            });
        
            const formData = new FormData();
            formData.append('message_content', message_content);
            formData.append('receipient', valObj("receipient_id_message"));
            formData.append('sender_type', "teacher");
            formData.append('receipient_type', "student");
            xhr.send(formData);
    }
}