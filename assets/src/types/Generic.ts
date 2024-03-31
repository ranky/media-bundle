import React from 'react';

/*
Examples WithChildren
=====================

type CardProps = WithChildren<{
  title: string
}>;

type CardProps = { title: string } & WithChildren;

*/

export type WithChildren<T = Record<string, unknown>> = T & { children?: React.ReactNode };

export type HTMLElementEvent<T extends HTMLElement> = Event & {
  target: T;
  currentTarget: T;
};

export type Immutable<T> = {
  readonly [K in keyof T]: Immutable<T[K]>
};

export type MakeRequired<T, K extends keyof T> = Omit<T, K> & Required<{ [P in K]: T[P] }>;
