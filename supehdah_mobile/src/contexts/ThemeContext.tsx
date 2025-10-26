import React from 'react';
import { DefaultTheme, NavigationContainer } from '@react-navigation/native';
import { scaleSize } from '../utils/scaling';

// Custom theme with scaled values
const MyTheme = {
  ...DefaultTheme,
  colors: {
    ...DefaultTheme.colors,
    primary: '#FF9EB1',
    background: '#F3F4F6',
    card: '#FFFFFF',
    text: '#333333',
  },
  // Custom sizes used throughout the app
  sizes: {
    icon: scaleSize(28),
    tabBarHeight: scaleSize(70),
    headerHeight: scaleSize(60),
    buttonHeight: scaleSize(50),
    cardRadius: scaleSize(16),
    padding: scaleSize(16),
    margin: scaleSize(16),
    fontSize: {
      small: scaleSize(12),
      medium: scaleSize(14),
      large: scaleSize(16),
      xlarge: scaleSize(18),
      xxlarge: scaleSize(22),
    },
  }
};

// Create a context for the theme
export const ThemeContext = React.createContext(MyTheme);

// Theme provider component
export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  return (
    <ThemeContext.Provider value={MyTheme}>
      <NavigationContainer theme={MyTheme}>
        {children}
      </NavigationContainer>
    </ThemeContext.Provider>
  );
};

// Custom hook to use the theme
export const useTheme = () => React.useContext(ThemeContext);