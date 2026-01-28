import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js";

const dateInput = document.querySelector<HTMLInputElement>("[data-date-picker]");

if (dateInput) {
  flatpickr(dateInput, {
    dateFormat: "d/m/Y",
    locale: Spanish,
    allowInput: false,
    disableMobile: true
  });
}
