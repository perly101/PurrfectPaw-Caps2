// screens/SplashScreen.tsx
import React from 'react';
import { View, Image, StyleSheet, ActivityIndicator } from 'react-native';

// Renamed to AppSplashScreen to avoid conflicts with Expo's SplashScreen
const AppSplashScreen: React.FC = () => {
  return (
    <View style={styles.container}>
      <Image source={require('../assets/purrfectpaw_logo.png')} style={styles.logo} />
      <ActivityIndicator size="large" color="#FFC1CC" style={styles.spinner} />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
  },
  logo: {
    width: 150,
    height: 150,
    resizeMode: 'contain',
    marginBottom: 30,
  },
  spinner: {
    marginTop: 20,
  },
});

export default AppSplashScreen;