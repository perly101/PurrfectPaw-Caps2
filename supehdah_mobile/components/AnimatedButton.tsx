import React, { useState } from 'react';
import {
  TouchableOpacity,
  Text,
  StyleSheet,
  ViewStyle,
  TextStyle,
  ActivityIndicator,
  Animated,
  View,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';

interface AnimatedButtonProps {
  onPress: () => void;
  text: string;
  style?: ViewStyle;
  textStyle?: TextStyle;
  loading?: boolean;
  disabled?: boolean;
  icon?: string;
  iconPosition?: 'left' | 'right';
  variant?: 'primary' | 'secondary' | 'outline';
}

// Define our main colors
const PINK = '#FF9EB1';
const DARK = '#3A3A3A';

const AnimatedButton: React.FC<AnimatedButtonProps> = ({
  onPress,
  text,
  style,
  textStyle,
  loading = false,
  disabled = false,
  icon,
  iconPosition = 'right',
  variant = 'primary',
}) => {
  const [scaleAnim] = useState(new Animated.Value(1));
  
  const handlePressIn = () => {
    Animated.timing(scaleAnim, {
      toValue: 0.95,
      duration: 100,
      useNativeDriver: true,
    }).start();
  };
  
  const handlePressOut = () => {
    Animated.timing(scaleAnim, {
      toValue: 1,
      duration: 150,
      useNativeDriver: true,
    }).start();
  };

  // Determine styles based on variant
  let buttonStyle, buttonTextStyle;
  
  switch (variant) {
    case 'secondary':
      buttonStyle = styles.secondaryButton;
      buttonTextStyle = styles.secondaryButtonText;
      break;
    case 'outline':
      buttonStyle = styles.outlineButton;
      buttonTextStyle = styles.outlineButtonText;
      break;
    default:
      buttonStyle = styles.primaryButton;
      buttonTextStyle = styles.primaryButtonText;
  }

  return (
    <Animated.View
      style={[
        { transform: [{ scale: scaleAnim }] },
        styles.buttonContainer,
      ]}
    >
      <TouchableOpacity
        activeOpacity={0.8}
        onPress={onPress}
        onPressIn={handlePressIn}
        onPressOut={handlePressOut}
        disabled={loading || disabled}
        style={[
          buttonStyle,
          disabled && styles.disabledButton,
          style,
        ]}
      >
        {loading ? (
          <ActivityIndicator color={variant === 'outline' ? PINK : '#FFFFFF'} />
        ) : (
          <View style={styles.buttonContent}>
            {icon && iconPosition === 'left' && (
              <Ionicons 
                name={icon as any} 
                size={18} 
                color={variant === 'outline' ? PINK : '#FFFFFF'} 
                style={styles.iconLeft} 
              />
            )}
            <Text style={[buttonTextStyle, textStyle]}>
              {text}
            </Text>
            {icon && iconPosition === 'right' && (
              <Ionicons 
                name={icon as any} 
                size={18} 
                color={variant === 'outline' ? PINK : '#FFFFFF'} 
                style={styles.iconRight} 
              />
            )}
          </View>
        )}
      </TouchableOpacity>
    </Animated.View>
  );
};

const styles = StyleSheet.create({
  buttonContainer: {
    width: '100%',
  },
  primaryButton: {
    backgroundColor: PINK,
    width: '100%',
    borderRadius: 16,
    paddingVertical: 18,
    alignItems: 'center',
    marginBottom: 24,
    marginTop: 10,
    shadowColor: PINK,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.4,
    shadowRadius: 15,
    elevation: 8,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
  },
  primaryButtonText: {
    color: '#FFFFFF',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.5,
  },
  secondaryButton: {
    backgroundColor: '#FFFFFF',
    width: '100%',
    borderRadius: 16,
    paddingVertical: 18,
    alignItems: 'center',
    marginBottom: 24,
    marginTop: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  secondaryButtonText: {
    color: DARK,
    fontSize: 16,
    fontWeight: '600',
  },
  outlineButton: {
    backgroundColor: 'transparent',
    width: '100%',
    borderRadius: 16,
    paddingVertical: 16,
    alignItems: 'center',
    marginBottom: 16,
    borderWidth: 2,
    borderColor: PINK,
  },
  outlineButtonText: {
    color: PINK,
    fontSize: 16,
    fontWeight: 'bold',
  },
  disabledButton: {
    opacity: 0.6,
  },
  buttonContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconLeft: {
    marginRight: 10,
  },
  iconRight: {
    marginLeft: 10,
  },
});

export default AnimatedButton;