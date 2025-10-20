import { Animated } from 'react-native';

// Types for React Navigation screen transitions
interface ScreenTransitionProps {
  current: {
    progress: Animated.AnimatedInterpolation<number>;
  };
  layouts: {
    screen: {
      width: number;
      height: number;
    };
  };
  next?: {
    progress: Animated.AnimatedInterpolation<number>;
  };
}

// Custom screen transition options for React Navigation
export const screenTransitionOptions = {
  // For stack navigation transitions
  stackTransition: {
    gestureEnabled: true,
    cardStyleInterpolator: ({ current, layouts }: ScreenTransitionProps) => {
      return {
        cardStyle: {
          transform: [
            {
              translateX: current.progress.interpolate({
                inputRange: [0, 1],
                outputRange: [layouts.screen.width, 0],
              }),
            },
          ],
        },
        overlayStyle: {
          opacity: current.progress.interpolate({
            inputRange: [0, 1],
            outputRange: [0, 0.5],
          }),
        },
      };
    },
    transitionSpec: {
      open: {
        animation: 'spring',
        config: {
          stiffness: 1000,
          damping: 50,
          mass: 3,
          overshootClamping: true,
          restDisplacementThreshold: 0.01,
          restSpeedThreshold: 0.01,
        },
      },
      close: {
        animation: 'timing',
        config: {
          duration: 250,
        },
      },
    },
  },
  
  // For modal-style screen transitions (from bottom)
  modalTransition: {
    gestureEnabled: true,
    cardStyleInterpolator: ({ current, layouts }: ScreenTransitionProps) => {
      return {
        cardStyle: {
          transform: [
            {
              translateY: current.progress.interpolate({
                inputRange: [0, 1],
                outputRange: [layouts.screen.height, 0],
              }),
            },
          ],
        },
        overlayStyle: {
          opacity: current.progress.interpolate({
            inputRange: [0, 1],
            outputRange: [0, 0.5],
          }),
        },
      };
    },
    transitionSpec: {
      open: {
        animation: 'spring',
        config: {
          stiffness: 1000,
          damping: 70,
          mass: 3,
          overshootClamping: true,
          restDisplacementThreshold: 0.01,
          restSpeedThreshold: 0.01,
        },
      },
      close: {
        animation: 'timing',
        config: {
          duration: 250,
        },
      },
    },
  },
  
  // For fade transition
  fadeTransition: {
    gestureEnabled: false,
    cardStyleInterpolator: ({ current }: ScreenTransitionProps) => ({
      cardStyle: {
        opacity: current.progress,
      },
    }),
    transitionSpec: {
      open: {
        animation: 'timing',
        config: { duration: 300 },
      },
      close: {
        animation: 'timing',
        config: { duration: 200 },
      },
    },
  },
};

interface AnimatedValues {
  fadeAnim: Animated.Value;
  slideAnim: Animated.Value;
  scaleAnim: Animated.Value;
}

// Animation utilities for screen components
export const createAnimatedValues = (): AnimatedValues => {
  return {
    fadeAnim: new Animated.Value(0),
    slideAnim: new Animated.Value(50),
    scaleAnim: new Animated.Value(0.95),
  };
};

export const animateIn = (animations: AnimatedValues) => {
  const { fadeAnim, slideAnim, scaleAnim } = animations;
  
  return Animated.parallel([
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 500,
      useNativeDriver: true,
    }),
    Animated.timing(slideAnim, {
      toValue: 0,
      duration: 500,
      useNativeDriver: true,
    }),
    Animated.timing(scaleAnim, {
      toValue: 1,
      duration: 500,
      useNativeDriver: true,
    }),
  ]);
};

export const animateOut = (animations: AnimatedValues, callback?: () => void) => {
  const { fadeAnim, slideAnim, scaleAnim } = animations;
  
  return Animated.parallel([
    Animated.timing(fadeAnim, {
      toValue: 0,
      duration: 200,
      useNativeDriver: true,
    }),
    Animated.timing(slideAnim, {
      toValue: 30,
      duration: 200,
      useNativeDriver: true,
    }),
    Animated.timing(scaleAnim, {
      toValue: 0.97,
      duration: 200,
      useNativeDriver: true,
    }),
  ]).start(callback);
};

// Button press animation
export const buttonPressAnimation = (scaleAnim: Animated.Value) => {
  Animated.sequence([
    Animated.timing(scaleAnim, {
      toValue: 0.96,
      duration: 100,
      useNativeDriver: true,
    }),
    Animated.timing(scaleAnim, {
      toValue: 1,
      duration: 150,
      useNativeDriver: true,
    }),
  ]).start();
};