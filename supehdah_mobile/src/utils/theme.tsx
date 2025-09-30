import React, { createContext, useContext, useEffect, useState } from 'react';
import { Dimensions, ScaledSize } from 'react-native';
import {
  deviceWidth,
  deviceHeight,
  normalizeFont,
  normalize,
  spacing,
  getBreakpoint,
} from './responsive';

// Define our theme with all the values we want available throughout the app
export const theme = {
  // Main app colors
  colors: {
    primary: '#FFC1CC', // Pink
    primaryDark: '#E6A6B4',
    secondary: '#333', // Dark gray
    success: '#4CAF50',
    danger: '#F44336',
    warning: '#FF9800',
    info: '#2196F3',
    light: '#F5F5F5',
    dark: '#333333',
    gray: '#9E9E9E',
    lightGray: '#E0E0E0',
    white: '#FFFFFF',
    black: '#000000',
    transparent: 'transparent',
    backdrop: 'rgba(0, 0, 0, 0.5)',
  },
  
  // Font sizes that will be responsive
  typography: {
    h1: normalizeFont(24),
    h2: normalizeFont(20),
    h3: normalizeFont(18),
    h4: normalizeFont(16),
    body: normalizeFont(14),
    small: normalizeFont(12),
    tiny: normalizeFont(10),
  },

  // Spacing values for margin, padding, etc.
  spacing,

  // Border radiuses
  borderRadius: {
    small: normalize(4),
    medium: normalize(8),
    large: normalize(16),
    pill: normalize(999),
  },
  
  // Shadows for elevated components
  shadows: {
    small: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 1 },
      shadowOpacity: 0.2,
      shadowRadius: 1.41,
      elevation: 2,
    },
    medium: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 2 },
      shadowOpacity: 0.23,
      shadowRadius: 2.62,
      elevation: 4,
    },
    large: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 4 },
      shadowOpacity: 0.3,
      shadowRadius: 4.65,
      elevation: 8,
    },
  },

  // Responsive values based on screen size
  responsive: {
    windowWidth: deviceWidth,
    windowHeight: deviceHeight,
    isSmallDevice: deviceWidth < 375,
    breakpoint: getBreakpoint(),
  }
};

// Create a context for our theme
export const ThemeContext = createContext(theme);

// Create a provider for our theme context
export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [themeState, setThemeState] = useState(theme);

  // Update theme values when screen size changes
  useEffect(() => {
    const updateLayout = ({ window }: { window: ScaledSize; screen: ScaledSize }) => {
      setThemeState(prevTheme => ({
        ...prevTheme,
        responsive: {
          ...prevTheme.responsive,
          windowWidth: window.width,
          windowHeight: window.height,
          isSmallDevice: window.width < 375,
          breakpoint: getBreakpoint(),
        }
      }));
    };

    // Initial update
    updateLayout({ window: Dimensions.get('window'), screen: Dimensions.get('screen') });

    // Subscribe to dimension changes
    const subscription = Dimensions.addEventListener('change', updateLayout);

    // Cleanup
    return () => subscription?.remove();
  }, []);

  return (
    <ThemeContext.Provider value={themeState}>
      {children}
    </ThemeContext.Provider>
  );
};

// Custom hook to use our theme
export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (context === undefined) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};