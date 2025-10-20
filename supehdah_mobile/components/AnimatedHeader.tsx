import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Animated,
  Platform,
  StatusBar,
  SafeAreaView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';

interface AnimatedHeaderProps {
  title: string;
  showBackButton?: boolean;
  transparent?: boolean;
  rightComponent?: React.ReactNode;
  backgroundColor?: string;
  textColor?: string;
  largeTitle?: boolean;
  subtitle?: string;
}

// Define our main colors
const PINK = '#FF9EB1';
const DARK = '#3A3A3A';

const AnimatedHeader: React.FC<AnimatedHeaderProps> = ({
  title,
  showBackButton = true,
  transparent = false,
  rightComponent,
  backgroundColor = '#FFFFFF',
  textColor = DARK,
  largeTitle = false,
  subtitle,
}) => {
  const navigation = useNavigation();
  const [fadeAnim] = useState(new Animated.Value(0));
  const [translateAnim] = useState(new Animated.Value(20));

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }),
      Animated.timing(translateAnim, {
        toValue: 0,
        duration: 500,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  const handleBack = () => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 0,
        duration: 200,
        useNativeDriver: true,
      }),
      Animated.timing(translateAnim, {
        toValue: -20,
        duration: 200,
        useNativeDriver: true,
      }),
    ]).start(() => {
      navigation.goBack();
    });
  };

  const containerStyle = transparent
    ? styles.transparentContainer
    : [styles.container, { backgroundColor }];

  return (
    <SafeAreaView style={[containerStyle]}>
      <StatusBar
        barStyle={transparent || backgroundColor === 'transparent' ? 'light-content' : 'dark-content'}
        translucent={transparent}
        backgroundColor={transparent ? 'transparent' : backgroundColor}
      />
      <View style={styles.headerContent}>
        <View style={styles.leftContainer}>
          {showBackButton && (
            <TouchableOpacity
              onPress={handleBack}
              style={[
                styles.backButton,
                transparent && styles.transparentBackButton,
              ]}
            >
              <Ionicons
                name="arrow-back"
                size={24}
                color={transparent ? '#FFF' : textColor}
              />
            </TouchableOpacity>
          )}
        </View>

        {largeTitle ? (
          <Animated.View
            style={[
              styles.largeTitleContainer,
              {
                opacity: fadeAnim,
                transform: [{ translateY: translateAnim }],
              },
            ]}
          >
            <Text style={[styles.largeTitle, { color: textColor }]}>{title}</Text>
            {subtitle && (
              <Text style={[styles.subtitle, { color: transparent ? '#FFFFFF99' : '#66666699' }]}>
                {subtitle}
              </Text>
            )}
          </Animated.View>
        ) : (
          <Animated.View
            style={[
              styles.titleContainer,
              {
                opacity: fadeAnim,
                transform: [{ translateX: translateAnim }],
              },
            ]}
          >
            <Text
              style={[
                styles.title,
                { color: transparent ? '#FFF' : textColor },
              ]}
              numberOfLines={1}
            >
              {title}
            </Text>
          </Animated.View>
        )}

        <View style={styles.rightContainer}>
          {rightComponent && rightComponent}
        </View>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    paddingTop: Platform.OS === 'android' ? StatusBar.currentHeight : 0,
    width: '100%',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 5,
    zIndex: 1000,
  },
  transparentContainer: {
    paddingTop: Platform.OS === 'android' ? StatusBar.currentHeight : 0,
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    zIndex: 1000,
    backgroundColor: 'transparent',
  },
  headerContent: {
    height: 56,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
  },
  leftContainer: {
    width: 40,
    alignItems: 'flex-start',
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#F0F0F0',
    justifyContent: 'center',
    alignItems: 'center',
  },
  transparentBackButton: {
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
  },
  titleContainer: {
    flex: 1,
    alignItems: 'center',
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
    textAlign: 'center',
  },
  largeTitleContainer: {
    position: 'absolute',
    top: 50,
    left: 16,
    right: 16,
  },
  largeTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    marginBottom: 6,
  },
  subtitle: {
    fontSize: 16,
    fontWeight: '400',
  },
  rightContainer: {
    width: 40,
    alignItems: 'flex-end',
  },
});

export default AnimatedHeader;