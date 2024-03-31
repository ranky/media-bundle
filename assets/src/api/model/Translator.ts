import { ConfigResponseType } from '@rankyMedia/types/Config';
import { TranslationsType } from '@rankyMedia/types/Translations';

class Translator {
  private static instance: Translator;

  private _translations: TranslationsType;

  private constructor() {
  }

  public static getInstance(): Translator {
    if (!Translator.instance) {
      Translator.instance = new Translator();
    }

    return Translator.instance;
  }

  get translations(): TranslationsType {
    return this._translations;
  }

  public trans(message: LiteralUnion<keyof TranslationsType>, data: null | object = null): string {
    if (!Object.prototype.hasOwnProperty.call(this._translations, message)) {
      return message;
    }
    let translatedMessage = this._translations[message];

    if (data) {
      Object.keys(data).forEach((key) => {
        translatedMessage = translatedMessage.replace(`{${key}}`, data[key]);
      });
    }
    return translatedMessage;
  }

  public static fromApi(apiData: ConfigResponseType): Translator {
    const translator = new Translator();
    translator._translations = apiData.translations;
    Translator.instance = translator;

    return Translator.instance;
  }
}

export default Translator;
