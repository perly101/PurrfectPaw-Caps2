import { Animated, Platform, Easing } from 'react-native';

// Define animation timings
export const TIMING = {
  FAST: 200,
  NORMAL: 300,
  SLOW: 500,
  VERY_SLOW: 800,
};

// Easing presets
export const EASING = {
  EASE_OUT: Easing.bezier(0.0, 0.0, 0.2, 1),
  EASE_IN: Easing.bezier(0.4, 0.0, 1, 1),
  EASE_IN_OUT: Easing.bezier(0.4, 0.0, 0.2, 1),
  BOUNCE: Easing.bounce,
  ELASTIC: Easing.elastic(1),
};

// Animation utilities
export const Animations = {
  // Fade in animation
  fadeIn: (value: Animated.Value, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: 1,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_OUT,
    });
  },
  
  // Fade out animation
  fadeOut: (value: Animated.Value, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: 0,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_IN,
    });
  },
  
  // Slide in from bottom
  slideInFromBottom: (value: Animated.Value, distance = 100, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: 0,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_OUT,
    });
  },
  
  // Slide out to bottom
  slideOutToBottom: (value: Animated.Value, distance = 100, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: distance,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_IN,
    });
  },
  
  // Slide in from right
  slideInFromRight: (value: Animated.Value, distance = 100, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: 0,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_OUT,
    });
  },
  
  // Slide out to right
  slideOutToRight: (value: Animated.Value, distance = 100, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: distance,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_IN,
    });
  },
  
  // Scale up animation
  scaleUp: (value: Animated.Value, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: 1,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_OUT,
    });
  },
  
  // Scale down animation
  scaleDown: (value: Animated.Value, endValue = 0, duration = TIMING.NORMAL, delay = 0) => {
    return Animated.timing(value, {
      toValue: endValue,
      duration,
      delay,
      useNativeDriver: true,
      easing: EASING.EASE_IN,
    });
  },
  
  // Button press animation
  buttonPress: (value: Animated.Value) => {
    return Animated.sequence([
      Animated.timing(value, {
        toValue: 0.95,
        duration: 100,
        useNativeDriver: true,
        easing: EASING.EASE_IN,
      }),
      Animated.timing(value, {
        toValue: 1,
        duration: 150,
        useNativeDriver: true,
        easing: EASING.BOUNCE,
      }),
    ]);
  },
  
  // Spin animation (for loaders, etc.)
  spin: (value: Animated.Value, duration = 1500) => {
    return Animated.loop(
      Animated.timing(value, {
        toValue: 1,
        duration,
        useNativeDriver: true,
        easing: Easing.linear,
      })
    );
  },
  
  // Bounce animation
  bounce: (value: Animated.Value, toValue = 1.2, duration = TIMING.NORMAL) => {
    return Animated.sequence([
      Animated.timing(value, {
        toValue,
        duration: duration / 2,
        useNativeDriver: true,
        easing: EASING.EASE_IN_OUT,
      }),
      Animated.timing(value, {
        toValue: 1,
        duration: duration / 2,
        useNativeDriver: true,
        easing: EASING.BOUNCE,
      }),
    ]);
  },

  // Entrance animation for cards
  cardEntrance: (opacity: Animated.Value, scale: Animated.Value, translateY: Animated.Value, delay = 0) => {
    return Animated.parallel([
      Animated.timing(opacity, {
        toValue: 1,
        duration: TIMING.SLOW,
        delay,
        useNativeDriver: true,
        easing: EASING.EASE_OUT,
      }),
      Animated.timing(scale, {
        toValue: 1,
        duration: TIMING.SLOW,
        delay,
        useNativeDriver: true,
        easing: EASING.EASE_OUT,
      }),
      Animated.timing(translateY, {
        toValue: 0,
        duration: TIMING.SLOW,
        delay,
        useNativeDriver: true,
        easing: EASING.EASE_OUT,
      }),
    ]);
  },

  // Staggered entrance for lists
  staggeredListEntrance: (items: Animated.Value[], baseDelay = 50) => {
    const animations = items.map((value, index) => {
      return Animated.timing(value, {
        toValue: 1,
        duration: TIMING.NORMAL,
        delay: baseDelay * index,
        useNativeDriver: true,
        easing: EASING.EASE_OUT,
      });
    });
    
    return Animated.stagger(baseDelay, animations);
  },

  // Page transition with fade and slide
  pageTransition: {
    in: (fade: Animated.Value, slide: Animated.Value) => {
      return Animated.parallel([
        Animations.fadeIn(fade, TIMING.SLOW),
        Animations.slideInFromRight(slide, 50, TIMING.SLOW),
      ]);
    },
    out: (fade: Animated.Value, slide: Animated.Value, callback: () => void) => {
      return Animated.parallel([
        Animations.fadeOut(fade, TIMING.FAST),
        Animations.slideOutToRight(slide, 30, TIMING.FAST),
      ]).start(callback);
    },
  },
};

// Interface for screen transition props
interface NavTransitionProps {
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

// Screen transition for navigation
export const ScreenTransition = {
  // Default slide transition
  slide: {
    gestureEnabled: true,
    cardStyleInterpolator: ({ current, layouts }: NavTransitionProps) => {
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
      };
    },
  },
  
  // Modal from bottom transition
  modal: {
    gestureEnabled: true,
    cardStyleInterpolator: ({ current, layouts }: NavTransitionProps) => {
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
  },
  
  // Fade transition
  fade: {
    gestureEnabled: false,
    cardStyleInterpolator: ({ current }: NavTransitionProps) => ({
      cardStyle: {
        opacity: current.progress,
      },
    }),
  },

  // Advanced slide with dynamic properties
  advancedSlide: (direction: 'horizontal' | 'vertical' = 'horizontal') => ({
    gestureEnabled: true,
    gestureDirection: direction === 'horizontal' ? 'horizontal' : 'vertical',
    cardStyleInterpolator: ({ current, layouts, next }: NavTransitionProps) => {
      return {
        cardStyle: {
          transform: [
            {
              translateX: direction === 'horizontal'
                ? current.progress.interpolate({
                    inputRange: [0, 1],
                    outputRange: [layouts.screen.width, 0],
                  })
                : 0,
            },
            {
              translateY: direction === 'vertical'
                ? current.progress.interpolate({
                    inputRange: [0, 1],
                    outputRange: [layouts.screen.height, 0],
                  })
                : 0,
            },
            {
              scale: current.progress.interpolate({
                inputRange: [0, 1],
                outputRange: [0.95, 1],
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
        ...(next && {
          containerStyle: {
            opacity: next.progress.interpolate({
              inputRange: [0, 1],
              outputRange: [1, 0.5],
            }),
          },
        }),
      };
    },
  }),
};

export default Animations;