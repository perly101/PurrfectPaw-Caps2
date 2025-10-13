/**
 * This is a patch for @react-navigation/elements/lib/module/useFrameSize.js
 * It's intended to be copied over the original file to fix the 'require is not defined' error in web
 */

import { useWindowDimensions } from 'react-native';

// Mock implementation for web
export default function useFrameSize() {
  // Use window dimensions as fallback
  const dimensions = useWindowDimensions();
  
  return {
    width: dimensions.width,
    height: dimensions.height,
    x: 0,
    y: 0
  };
}