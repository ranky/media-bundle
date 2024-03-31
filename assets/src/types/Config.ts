import Settings from '@rankyMedia/api/model/Settings';
import Translator from '@rankyMedia/api/model/Translator';
import { TranslationsType } from '@rankyMedia/types/Translations';
import { WithChildren } from '@rankyMedia/types/Generic';
import MediaRepository from '@rankyMedia/api/repository/MediaRepository';

export interface ConfigType {
  locale: string
  upload_directory: string
  upload_url: string
  assets_prefix_url: string
  mime_types: string[]
  max_file_size: number
  pagination_limit: number
  supported_image_types: string[]
  placeholder_image_types: Record<string, string>
  image: {
    resize_driver: string
    resize_gif: boolean
    quality: number
    original_max_width: number
    breakpoints: {
      large: number[]
      medium: number[]
      small: number[]
      xsmall: number[]
    }
  }
}

export type ConfigResponseType = {
  config: ConfigType,
  translations: TranslationsType
};

export type MediaContextType = {
  settings: Settings,
  translator: Translator,
  mediaRepository: MediaRepository,
};

export type AppType = {
  title?: string;
  apiPrefix: string;
  selectionMode?: boolean;
  multipleSelection?: boolean;
  targetRef?: HTMLElement;
} & WithChildren;
