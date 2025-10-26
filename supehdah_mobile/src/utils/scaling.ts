import { Dimensions, PixelRatio } from 'react-native';

const { width, height } = Dimensions.get('window');

// Base dimensions based on standard mobile device
const baseWidth = 375;
const baseHeight = 812;

// Scaling factors
const widthScale = width / baseWidth;
const heightScale = height / baseHeight;
const scale = Math.min(widthScale, heightScale);

/**
 * Scales a size according to the device's screen size
 * @param size The size to scale
 * @returns The scaled size
 */
export function scaleSize(size: number): number {
  return Math.round(size * scale);
}

/**
 * Scales a font size according to the device's screen size
 * @param size The font size to scale
 * @returns The scaled font size
 */
export function scaleFontSize(size: number): number {
  const newSize = size * scale;
  return Math.round(PixelRatio.roundToNearestPixel(newSize));
}

/**
 * Returns horizontal spacing based on screen width
 * @param percentage The percentage of screen width
 * @returns The calculated spacing
 */
export function horizontalScale(percentage: number): number {
  return width * percentage / 100;
}

/**
 * Returns vertical spacing based on screen height
 * @param percentage The percentage of screen height
 * @returns The calculated spacing
 */
export function verticalScale(percentage: number): number {
  return height * percentage / 100;
}

/**
 * Creates responsive padding based on screen size
 * @returns Object with responsive padding values
 */
export function getResponsivePadding() {
  return {
    paddingHorizontal: scaleSize(20),
    paddingVertical: scaleSize(16),
  };
}