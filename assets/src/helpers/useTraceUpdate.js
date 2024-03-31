import { useEffect, useRef } from 'react';

/**
 * https://stackoverflow.com/questions/41004631/trace-why-a-react-component-is-re-rendering
 * @param props
 */
function useTraceUpdate(props) {
  const prev = useRef(props);
  useEffect(() => {
    const changedProps = Object.entries(props).reduce((ps, [k, v]) => {
      if (prev.current[k] !== v) {
        ps[k] = [prev.current[k], v];
      }
      return ps;
    }, {});
    if (Object.keys(changedProps).length > 0) {
      console.log('Changed props:', changedProps);
    }
    prev.current = props;
  });
}

export default useTraceUpdate;
