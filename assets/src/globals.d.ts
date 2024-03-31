import Uppy from '@uppy/core';
import React, { HTMLAttributes } from 'react';

declare global {

  namespace NodeJS {
    interface ProcessEnv {
      NODE_ENV: 'development' | 'production' | 'test';
      ASSETS_URL_PREFIX: string;
      UPPY_LOCALES: string; // webpack
    }
  }

  export type UppyWithLocales = Uppy & { locales: object };

  interface Window {
    uppy: Uppy;
    Uppy: { locales: object }
    LOCALE: string;
  }

  type ApiProblem = {
    status: number,
    title: string,
    type?: string,
    details?: Array<string>,
    causes?: Array<{ name: string, reason: string }>
  };

  interface Error {
    status: number;
    info: object;
  }

  type ValueOf<T> = T[keyof T];

  type LiteralUnion<T extends U, U = string> = T | (U & { _?: never });

}

declare module 'querystring' {
  export function stringify(val: object): string;

  export function parse(val: string): object;
}

declare namespace JSX {
  interface IntrinsicElements {
    'img': HTMLAttributes<string> & {
      alt: string,
      src: string,
      loading?: 'lazy' | 'eager' | 'auto';
    };
  }
}

type SvgrComponent = React.FunctionComponent<React.SVGAttributes<SVGElement>>;

/* declare module '*.svg' {
  const content: any;
  export default content;

} */
declare module '*.svg' {
  const ReactComponent: SvgrComponent;
  export { ReactComponent };
}

export {};
