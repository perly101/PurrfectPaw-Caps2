import React from 'react';
import { StyleSheet, View, Platform } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

interface SafeAreaLayoutProps {
  children: React.ReactNode;
  bottomTabHeight?: number;
}

/**
 * SafeAreaLayout component ensures proper spacing around system UI elements
 * like notches, home indicators, and navigation bars
 */
export const SafeAreaLayout = ({ 
  children,
  bottomTabHeight = 70
}: SafeAreaLayoutProps) => {
  const insets = useSafeAreaInsets();
  
  // Determine if device has hardware buttons (estimation for Android)
  const hasHardwareButtons = Platform.OS === 'android' && !insets.bottom;
  
  // Calculate the bottom padding needed
  const getBottomPadding = () => {
    if (Platform.OS === 'ios') {
      return bottomTabHeight + insets.bottom;
    }
    
    // For Android with hardware buttons, provide extra padding
    return bottomTabHeight + (hasHardwareButtons ? 30 : 15);
  };

  return (
    <View 
      style={[
        styles.container,
        {
          // Apply safe area insets padding
          paddingTop: insets.top,
          paddingLeft: insets.left,
          paddingRight: insets.right,
          paddingBottom: getBottomPadding(),
        }
      ]}
    >
      {children}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
});

export default SafeAreaLayout;