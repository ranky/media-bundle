import useSettings from '@rankyMedia/api/hook/useSettings';

type AssetType = {
  (url: string, isUploadedFile?: boolean): string;
};

const Asset: AssetType = (url, isUploadedFile = false) => {
  const settings = useSettings();

  if (isUploadedFile) {
    return url;
  }

  return `${settings.assetsPrefixUrl}${url}`;
};

export default Asset;
