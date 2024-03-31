import React, { useEffect, useRef } from 'react';
import './upload_floating_button.scss';
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import appConfig from '@rankyMedia/config';

type Props = {
  onClick: () => void
};

const UploadFloatingButton: React.FC<Props> = ({ onClick }): React.ReactElement => {
  const translator = useTranslator();
  const buttonRef = useRef<HTMLDivElement>();

  useEffect(() => {
    // position always bottom right
    const elementScroll = document.querySelector(appConfig.root_class);
    elementScroll.addEventListener('scroll', () => {
      if (buttonRef.current) {
        buttonRef.current.style.bottom = `${(-elementScroll.scrollTop + 20)}px`;
      }
    });
  }, []);

  return (
    <div ref={buttonRef} className="ranky-media__upload-floating-button">
      <button
        type="button"
        title={translator.trans('open_upload_dashboard')}
        aria-label={translator.trans('open_upload_dashboard')}
        onClick={onClick}
      >
        <svg aria-hidden="true" focusable="false" width="46" height="46" viewBox="0 0 32 32">
          <g fill="none" fillRule="evenodd">
            <rect className="uppy-ProviderIconBg" width="32" height="32" rx="16" fill="#2275D7" />
            <path
              d="M21.973 21.152H9.863l-1.108-5.087h14.464l-1.246 5.087zM9.935 11.37h3.958l.886 1.444a.673.673 0 0 0 .585.316h6.506v1.37H9.935v-3.13zm14.898 3.44a.793.793 0 0 0-.616-.31h-.978v-2.126c0-.379-.275-.613-.653-.613H15.75l-.886-1.445a.673.673 0 0 0-.585-.316H9.232c-.378 0-.667.209-.667.587V14.5h-.782a.793.793 0 0 0-.61.303.795.795 0 0 0-.155.663l1.45 6.633c.078.36.396.618.764.618h13.354c.36 0 .674-.246.76-.595l1.631-6.636a.795.795 0 0 0-.144-.675z"
              fill="#FFF"
            />
          </g>
        </svg>
      </button>
    </div>
  );
};

export default UploadFloatingButton;
