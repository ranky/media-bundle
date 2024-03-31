import './loader.scss';
import React, { CSSProperties } from 'react';

function Loader({ style = { } }: { style?: CSSProperties }) {
  return (<div id="page-loader" className="show" style={style} />);
}
export default Loader;
