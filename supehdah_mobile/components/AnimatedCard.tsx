import React, { useRef, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Animated,
  ViewStyle,
  TextStyle,
  ImageBackground,
  Image,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';

interface AnimatedCardProps {
  title: string;
  description?: string;
  onPress: () => void;
  style?: ViewStyle;
  titleStyle?: TextStyle;
  descriptionStyle?: TextStyle;
  icon?: string;
  iconColor?: string;
  background?: any; // Image source
  index?: number; // For staggered animation
  badge?: string | number;
}

const PINK = '#FF9EB1';
const DARK = '#3A3A3A';

const AnimatedCard: React.FC<AnimatedCardProps> = ({
  title,
  description,
  onPress,
  style,
  titleStyle,
  descriptionStyle,
  icon,
  iconColor = PINK,
  background,
  index = 0,
  badge,
}) => {
  // Animation values
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const scaleAnim = useRef(new Animated.Value(0.9)).current;
  const translateAnim = useRef(new Animated.Value(50)).current;
  const pressAnim = useRef(new Animated.Value(1)).current;
  
  useEffect(() => {
    // Staggered animation based on card index
    const delay = 100 + (index * 150);
    
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 600,
        delay,
        useNativeDriver: true,
      }),
      Animated.timing(scaleAnim, {
        toValue: 1,
        duration: 600,
        delay,
        useNativeDriver: true,
      }),
      Animated.timing(translateAnim, {
        toValue: 0,
        duration: 600,
        delay,
        useNativeDriver: true,
      }),
    ]).start();
  }, [fadeAnim, scaleAnim, translateAnim, index]);

  const handlePressIn = () => {
    Animated.timing(pressAnim, {
      toValue: 0.96,
      duration: 100,
      useNativeDriver: true,
    }).start();
  };

  const handlePressOut = () => {
    Animated.timing(pressAnim, {
      toValue: 1,
      duration: 150,
      useNativeDriver: true,
    }).start();
  };

  return (
    <Animated.View
      style={[
        styles.container,
        {
          opacity: fadeAnim,
          transform: [
            { scale: Animated.multiply(scaleAnim, pressAnim) },
            { translateY: translateAnim },
          ],
        },
        style,
      ]}
    >
      <TouchableOpacity
        style={styles.touchable}
        onPress={onPress}
        onPressIn={handlePressIn}
        onPressOut={handlePressOut}
        activeOpacity={0.9}
      >
        {background ? (
          <ImageBackground
            source={background}
            style={styles.backgroundImage}
            imageStyle={styles.backgroundImageStyle}
          >
            <View style={styles.contentWithBackground}>
              {icon && (
                <View style={styles.iconContainer}>
                  <Ionicons name={icon as any} size={28} color={iconColor} />
                </View>
              )}
              <View style={styles.textContainer}>
                <Text style={[styles.titleWithBackground, titleStyle]}>{title}</Text>
                {description && (
                  <Text style={[styles.descriptionWithBackground, descriptionStyle]}>
                    {description}
                  </Text>
                )}
              </View>
              
              {badge && (
                <View style={styles.badgeContainer}>
                  <Text style={styles.badgeText}>{badge}</Text>
                </View>
              )}
            </View>
          </ImageBackground>
        ) : (
          <View style={styles.content}>
            {icon && (
              <View style={styles.iconContainer}>
                <Ionicons name={icon as any} size={28} color={iconColor} />
              </View>
            )}
            <View style={styles.textContainer}>
              <Text style={[styles.title, titleStyle]}>{title}</Text>
              {description && (
                <Text style={[styles.description, descriptionStyle]}>
                  {description}
                </Text>
              )}
            </View>
            
            {badge && (
              <View style={styles.badgeContainer}>
                <Text style={styles.badgeText}>{badge}</Text>
              </View>
            )}
          </View>
        )}
      </TouchableOpacity>
    </Animated.View>
  );
};

const styles = StyleSheet.create({
  container: {
    borderRadius: 16,
    marginBottom: 16,
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 4,
    },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 5,
    overflow: 'hidden',
  },
  touchable: {
    width: '100%',
    height: '100%',
  },
  backgroundImage: {
    width: '100%',
    height: '100%',
    justifyContent: 'flex-end',
  },
  backgroundImageStyle: {
    borderRadius: 16,
  },
  content: {
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
  },
  contentWithBackground: {
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.4)',
    height: '100%',
  },
  iconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: 'rgba(255,158,177,0.15)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  textContainer: {
    flex: 1,
  },
  title: {
    fontSize: 16,
    fontWeight: '600',
    color: DARK,
    marginBottom: 4,
  },
  titleWithBackground: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFFFFF',
    marginBottom: 4,
  },
  description: {
    fontSize: 14,
    color: '#666666',
  },
  descriptionWithBackground: {
    fontSize: 14,
    color: 'rgba(255,255,255,0.8)',
  },
  badgeContainer: {
    backgroundColor: PINK,
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 3,
    justifyContent: 'center',
    alignItems: 'center',
    minWidth: 24,
  },
  badgeText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
  },
});

export default AnimatedCard;