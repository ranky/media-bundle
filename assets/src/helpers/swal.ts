import Swal, { SweetAlertResult } from 'sweetalert2';
import Translator from '@rankyMedia/api/model/Translator';
import ApiProblemError from '@rankyMedia/api/model/ApiProblemError';

function successAlert(message:string = null): Promise<SweetAlertResult> {
  const translator = Translator.getInstance();
  return Swal.fire({
    title: message || translator.trans('swal.successfully'),
    icon: 'success',
    confirmButtonColor: '#6f9c40',
    timer: 1500,
  });
}

function errorAlert(error: ApiProblemError): Promise<SweetAlertResult> {
  const title = error.hasCauses() ? error.message : `Error ${error.status}`;
  const html = error.hasCauses() ? error.causesToHtml() : error.message;

  return Swal.fire({
    title,
    html,
    icon: 'error',
    confirmButtonColor: '#DD6B55',
  });
}

function confirmDeleteAlert(successCallback: () => void, cancelCallback:() => void = null, message: string = null)
  : Promise<void | Record<string, never> | SweetAlertResult> {
  const translator = Translator.getInstance();
  return Swal.fire({
    title: message || translator.trans('swal.confirm_delete'),
    text: translator.trans('swal.delete_text'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    confirmButtonText: translator.trans('swal.confirm_button'),
    cancelButtonText: translator.trans('swal.cancel_button'),
  }).then(async (result) => {
    if (result.value) {
      return successCallback();
    }
    if (cancelCallback !== null) {
      return cancelCallback();
    }
    return {};
  });
}

export { errorAlert, confirmDeleteAlert, successAlert };
