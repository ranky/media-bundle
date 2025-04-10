import React, { ReactElement } from 'react';
import ReactDOM from 'react-dom';
import appConfig from '@rankyMedia/config';
import { on } from '@rankyMedia/helpers/event';
import App from './App';
import { MediaManagerModal, MediaManager, MediaFormPreview } from './index';

const reactVersion = React.version.split('.')[0];
let Render: (component: ReactElement, container: HTMLElement) => void;
if (reactVersion === '18') {
  Render = (component: ReactElement, container: HTMLElement) => {
    // @ts-expect-error createRoot is only available in React 18
    const root = ReactDOM.createRoot(container);
    root.render(component);
  };
} else {
  Render = (component: ReactElement, container: HTMLElement) => {
    ReactDOM.render(component, container);
  };
}

/**
 * Render MediaFormPreview component for each form type
 */
const renderMediaFormPreviews = () => {
  document.querySelectorAll<HTMLDivElement>('.ranky-media-form-type__content')
    .forEach((formSelect) => {
      const isMultipleSelection = formSelect.getAttribute('data-multiple-selection') === 'true'
                || formSelect.getAttribute('data-multiple-selection') === '1';
      const fieldId = formSelect.getAttribute('data-field-id');
      const previewJustification = formSelect.getAttribute('data-preview-justification') || 'center';
      const buttonJustification = formSelect.getAttribute('data-button-justification') || 'flex-end';

      Render(
        <App
          selectionMode
          title={formSelect.getAttribute('data-title') || ''}
          multipleSelection={isMultipleSelection}
          apiPrefix={formSelect.getAttribute('data-api-prefix') || ''}
          targetRef={formSelect}
        >
          <MediaFormPreview
            fieldId={fieldId}
            previewJustification={previewJustification}
            buttonJustification={buttonJustification}
            predefinedData={(formSelect.parentElement.querySelector(`input#${fieldId}`) as HTMLInputElement)?.value || null}
          />
        </App>,
        formSelect,
      );
    });
};

/**
 * Add and render MediaManagerModal component
 */
const renderMediaManagerModal = (event: Event) => {
  const element = event.target as HTMLElement;
  event.preventDefault();
  const isMultipleSelection = element.getAttribute('data-multiple-selection') === 'true'
        || element.getAttribute('data-multiple-selection') === '1';
  const modal = document.createElement('div');
  modal.classList.add('wrapper-ranky-media-modal');
  document.body.appendChild(modal);
  Render(
    <App
      selectionMode
      title={element.getAttribute('data-title') || ''}
      multipleSelection={isMultipleSelection}
      apiPrefix={element.getAttribute('data-api-prefix') || ''}
      targetRef={element}
    >
      <MediaManagerModal
        onCloseModal={() => {
          modal.remove();
        }}
      />
    </App>,
    modal,
  );
};

/**
 * MutationObserver for MediaFormPreview
 * Render MediaFormPreview component for each form type when a new form type is added.
 * Example: Symfony form collection
 */
const MediaFormPreviewObserver = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.type === 'childList') {
      mutation.addedNodes.forEach((node) => {
        const rankyMediaFormElement = node instanceof HTMLElement
                    && node.querySelector('.ranky-media-form-type__content');
        if (rankyMediaFormElement) {
          renderMediaFormPreviews();
        }
      });
    }
  });
});

MediaFormPreviewObserver.observe(document.body, {
  childList: true,
  subtree: true,
});

renderMediaFormPreviews();

on(document, '.ranky-media-open-modal', 'click', (event: Event) => {
  renderMediaManagerModal(event);
});

if (document.querySelector(appConfig.root_class)) {
  Render(
    <App apiPrefix={document.querySelector(appConfig.root_class).getAttribute('data-api-prefix') || ''}>
      <MediaManager />
    </App>,
    document.querySelector(appConfig.root_class),
  );
}
