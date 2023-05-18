//SIDEBAR
const btnSidebar = document.querySelector('.btn-sidebar');
const sidebar = document.querySelector('.sidebar');
const sidebarContent = document.querySelector('.sidebar-content-wrapper');
const sidebarFooter = document.querySelector('.user-profile-footer');
const btnLogout = document.querySelector('.btn-user-logout');

btnSidebar?.addEventListener('click', () => {
  // Toggle sidebar class with tailwind
  sidebar.classList.toggle('w-full');
  sidebar.classList.toggle('tablet:w-[30rem]');
  sidebar.classList.toggle('w-14');
  sidebar.classList.toggle('bg-aside');
  //show content
  sidebarContent.classList.toggle('opacity-0');
  //hide logout btn
  btnLogout.classList.toggle('hidden');
  //sidebar footer
  sidebarFooter.classList.toggle('justify-between');
  sidebarFooter.classList.toggle('justify-center');

  // rotate btn
  btnSidebar.classList.toggle('fa-flip-horizontal');
  btnSidebar.classList.toggle('btn-aside');
});

// USER PIN

const editBox = document.querySelector('.pin-edit-box');

export const toggleEditBox = (closeBox = false) => {
  const editBoxAll = document.querySelectorAll('.pin-edit-box');
  //to prevent from toggling and simple hide if closeBox
  closeBox
    ? editBoxAll.forEach(box => box.classList.add('hidden'))
    : editBox?.classList.toggle('hidden');
};

const attachEditBtnListener = () => {
  const btnEditPin = document.querySelectorAll('.fa-ellipsis');

  btnEditPin?.forEach(btn => {
    btn.addEventListener('click', e => {
      //find the parent
      const userPin = e.target.closest('.user-pin');
      //find the edit box in the parent
      const editBox = userPin.querySelector('.pin-edit-box');

      // toggle edit box
      if (editBox) {
        editBox.classList.toggle('hidden');
        console.log(editBox.classList + 'second');
      }

      //prevents from bubbling to the parent
      e.stopPropagation();
    });
  });
};

export default attachEditBtnListener;

editBox?.addEventListener('click', e => {
  //prevents from bubbling to the grandparent
  e.stopPropagation();
  //close the box
  e.target.tagName === 'LI' ? toggleEditBox() : null;
});

//close the edit on global click
document.body.addEventListener('click', e => {
  !e.target.classList.contains('.pin-edit-box_item')
    ? toggleEditBox(close)
    : null;
});

//FORM ERROR HANDLER

// Get the URL query string
const queryString = window.location.search;

// Check if the query string contains the word "error"
const urlHasError = queryString.includes('error');
let isError = false;

if (urlHasError) {
  isError = true;
} else {
  isError = false;
}

//FORM ANIMATION
const inputs = document.querySelectorAll('.input-field');

inputs.forEach(function (input) {
  const label = input.previousElementSibling;

  let inputHasValue = false;
  let hasFocus = false;
  input.addEventListener('focus', function () {
    if (hasFocus && !inputHasValue) {
    }
    label.classList.add('input-focused');
    hasFocus = true;
  });

  input.addEventListener('blur', function () {
    if (inputHasValue) {
      return;
    }
    label.classList.remove('input-focused');
    hasFocus = false;
  });

  input.addEventListener('input', function () {
    if (input.value.trim().length > 0) {
      inputHasValue = true;

      label.classList.add('input-focused');
    } else {
      inputHasValue = false;

      if (hasFocus) {
        return;
      }
      label.classList.remove('input-focused');
    }
  });
});
