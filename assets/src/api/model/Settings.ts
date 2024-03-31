import { ConfigResponseType } from '@rankyMedia/types/Config';

class Settings {
  private static instance: Settings;

  private _title: string | null = null;

  private _targetRef: HTMLElement | null = null;

  private _selectionMode: boolean = false;

  private _multipleSelection: boolean = false;

  private _apiPrefix: string;

  private _assetsPrefixUrl: string = process.env.ASSETS_URL_PREFIX;

  private _mimeTypes!: string[];

  private _supportedImageTypes: string[];

  private _placeholderImageTypes: Record<string, string>;

  private _maxFileSize!: number | null;

  private _paginationLimit: number;

  private _locale: string = 'en';

  private _uploadUrl!: string;

  private constructor() {
  }

  public static getInstance(): Settings {
    if (!Settings.instance) {
      Settings.instance = new Settings();
    }

    return Settings.instance;
  }

  get targetRef(): HTMLElement | null {
    return this._targetRef;
  }

  static set targetRef(targetRef: HTMLElement | null) {
    Settings.getInstance()._targetRef = targetRef;
  }

  get title(): string | null {
    return this._title;
  }

  static set title(title: string) {
    Settings.getInstance()._title = title;
  }

  get isSelectionMode(): boolean {
    return this._selectionMode;
  }

  static set selectionMode(selectionMode: boolean) {
    Settings.getInstance()._selectionMode = selectionMode;
  }

  get isMultipleSelection(): boolean {
    return this._multipleSelection;
  }

  static set multipleSelection(multipleSelection: boolean) {
    Settings.getInstance()._multipleSelection = multipleSelection;
  }

  static set apiPrefix(apiPrefix: string) {
    Settings.getInstance()._apiPrefix = apiPrefix;
  }

  get apiPrefix(): string {
    return this._apiPrefix;
  }

  get locale(): string {
    return this._locale;
  }

  get supportedImageTypes(): string[] {
    return this._supportedImageTypes;
  }

  get placeholderImageTypes(): Record<string, string> {
    return this._placeholderImageTypes;
  }

  get paginationLimit(): number {
    return this._paginationLimit;
  }

  get uploadUrl(): string {
    return this._uploadUrl;
  }

  get assetsPrefixUrl(): string {
    return this._assetsPrefixUrl;
  }

  get maxFileSize(): number | null {
    return this._maxFileSize;
  }

  get mimeTypes(): string[] {
    return this._mimeTypes;
  }

  public static fromApi(apiData: ConfigResponseType): Settings {
    const settings = new Settings();
    settings._maxFileSize = apiData.config?.max_file_size || null;
    settings._mimeTypes = apiData.config?.mime_types || [];
    settings._uploadUrl = apiData.config.upload_url;
    settings._locale = apiData.config.locale;
    settings._supportedImageTypes = apiData.config?.supported_image_types || [];
    settings._placeholderImageTypes = apiData.config?.placeholder_image_types || {};
    settings._paginationLimit = apiData.config.pagination_limit;
    Settings.instance = settings;

    return Settings.instance;
  }
}

export default Settings;
