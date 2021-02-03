//swiper
var mySwiper = new Swiper('.swiper-container', {
    slidesPerView: 1,
    breakpoints: {
        1280: {
            slidesPerView: 2,
            spaceBetween: 20,
        }
    }
})

//variables
const addBtn = document.querySelector('.add-btn');
const addBtnWrapper = document.querySelector('.add-btn-wrapper');
const formContainer = document.querySelector('.add-subject-container');
const trashBtn = document.querySelector('.delete-btn');
const deleteSpans = document.querySelectorAll('.delete-span');
const tasksHintBtn = document.querySelector('.close-tasks-hint-btn');
const timetableHintBtn = document.querySelector('.close-timetable-hint-btn');
const hintTimetableBlock = document.querySelector('.hint-timetable-block');
const hintTasksBlock = document.querySelector('.hint-tasks-block');

//functions
function revealForm() {
    formContainer.style.display = 'flex';
    addBtnWrapper.remove();
}

function revealSpans() {
    deleteSpans.forEach(span => {
        let display = getComputedStyle(span).display;
        if(display == 'none') {
            span.style.display = 'inline';
        } else {
            span.style.display = 'none';
        }
    });
}

function hideTimetableHint() {
    localStorage.setItem("timetable-hint-hide", "true");
    checkHintStatus();
}

function hideTasksHint() {
    localStorage.setItem("tasks-hint-hide", "true");
    checkHintStatus();
}

function checkHintStatus() {
    if(localStorage.getItem("timetable-hint-hide") === "true" && hintTimetableBlock !== null) {
        hintTimetableBlock.remove();
    }
    if(localStorage.getItem("tasks-hint-hide") === "true" && hintTasksBlock !== null) {
        hintTasksBlock.remove();
    }
}

//event listeners
addBtn.addEventListener('click', revealForm);
trashBtn.addEventListener('click', revealSpans);

if(tasksHintBtn !== null) {
    tasksHintBtn.addEventListener('click', hideTasksHint);
}

if(timetableHintBtn !== null) {
    timetableHintBtn.addEventListener('click', hideTimetableHint);
}

checkHintStatus();