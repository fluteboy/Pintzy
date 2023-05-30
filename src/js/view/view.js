import GuestEditor from '../user/guest.editor.js';
import FormView from './form.view.js';
import FormEditorView from './formEditor.view.js';
import Map from './map.view.js';
import Pin from './pin.view.js';

/**
 * form UI
 * Sidebar UI
 * map
 */
export default class View {
  guestEditor;
  form;
  formEditor;
  map;
  guestPins = [];
  userPins = [];
  globalPins = [];
  pinClass;

  constructor(guestState, userState, globalPins) {
    this.form = new FormView();
    this.formEditor = new FormEditorView();
    this.guestEditor = new GuestEditor();
    this.guestPins = guestState;
    this.userPins = userState;
    this.globalPins = globalPins;
    this.renderForm = this.renderForm.bind(this);
    this.hideForm = this.hideForm.bind(this);
    this.map = new Map(
      this.guestPins,
      this.userPins,
      this.globalPins,
      this.renderForm,
      this.renderSpinner
    );
    this.newEvHandler = this.map.newMapEvHandler;
    this.hideForm();
  }

  renderMap() {
    this.map.getPosition(map => {
      if (map) {
        this.pinClass = new Pin(
          map,
          this.guestPins,
          this.usrePins,
          this.globalPins
        );
      } else {
        console.log('No map');
        // Handle the case where map is not available
      }
    });
  }

  renderForm(mapEvent) {
    this.form.showFormHandler(mapEvent, this.newEvHandler);
  }

  hideForm() {
    this.form.hideFormHandler();
  }

  renderEditFormHandler() {
    this.formEditor.showForm();
  }

  renderSpinner() {
    const spinner = document.querySelector('.spinner');
    spinner.classList.add('hidden');
    spinner.classList.remove('spin');
    spinner.classList.remove('z-20');
  }

  editBtnGlobalHandler(pinClass) {
    pinClass?.editBtnGlobalHandler();
  }
}
