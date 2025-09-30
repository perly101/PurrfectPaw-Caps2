// screens/LoginScreen.tsx
import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  ImageBackground,
  Image,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { API } from '../src/api';
import { OtpApi } from '../src/otpApi';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { RootStackParamList } from '../App'; // ensure this declares 'PersonalTabs' and 'Login'

const PINK = '#FFC1CC';
const DARK = '#333';

export default function LoginScreen(): React.ReactElement {
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();

  React.useEffect(() => {
    // Check if the user has a pending OTP verification
    const checkVerificationStatus = async () => {
      const hasVerificationPending = await OtpApi.hasVerificationPending();
      
      if (hasVerificationPending) {
        // Get the user info to extract email
        try {
          const userResponse = await API.get('/user');
          const userEmail = userResponse.data.email;
          
          // Navigate to OTP verification screen if verification is pending
          Alert.alert(
            'Email Verification Required',
            'Please complete your email verification.',
            [
              {
                text: 'OK',
                onPress: () => {
                  navigation.navigate('OTPVerification', { email: userEmail });
                }
              }
            ]
          );
        } catch (error) {
          console.error('Error fetching user data:', error);
        }
      }
    };
    
    checkVerificationStatus();
  }, [navigation]);

  const handleLogin = async (): Promise<void> => {
    try {
      setLoading(true);

      // Clear any existing tokens before login
      await AsyncStorage.removeItem('token');
      await AsyncStorage.removeItem('userToken');
      await AsyncStorage.removeItem('accessToken');

      const res = await API.post('/login', { email, password });

      // Get the token from the response
      const token = res.data?.token ?? res.data?.access_token ?? res.data?.data?.token;
      if (!token) throw new Error('No token returned from server');

      // Store the token in AsyncStorage first
      await AsyncStorage.setItem('token', token);
      
      // Also save as userToken for backwards compatibility
      await AsyncStorage.setItem('userToken', token);

      // Wait a moment for the token to be properly stored
      await new Promise(resolve => setTimeout(resolve, 100));

      // Set the token in the API headers immediately
      API.defaults.headers = API.defaults.headers || {};
      API.defaults.headers.common = API.defaults.headers.common || {};
      API.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      console.log('🔐 Successfully stored auth token and updated headers');

      // Extract user info directly from the login response if available
      let userInfo = res.data?.user;
      let isEmailVerified = userInfo?.email_verified_at !== null;
      let userEmail = userInfo?.email || email;
      
      // Only make the additional /user request if we don't have complete user info
      if (!userInfo || typeof userInfo.email_verified_at === 'undefined') {
        try {
          console.log('📥 Fetching additional user details...');
          const userResponse = await API.get('/user');
          userInfo = userResponse.data;
          isEmailVerified = userInfo?.email_verified_at !== null;
          userEmail = userInfo?.email || email;
        } catch (userErr) {
          console.error('⚠️ Error fetching user details, using available info:', userErr);
          // Continue with what we have
        }
      }
      
      // Check if user needs email verification
      if (!isEmailVerified) {
        console.log('📧 Email verification required');
        // Set verification pending flag
        await OtpApi.setVerificationPending();
        
        // Store the fact that we're in a verification flow
        await AsyncStorage.setItem('verification_flow', 'true');
        
        Alert.alert(
          'Email Verification Required',
          'Please verify your email address before proceeding.',
          [
            {
              text: 'OK',
              onPress: () => {
                navigation.navigate('OTPVerification', { email: userEmail });
              }
            }
          ]
        );
      } else {
        console.log('✅ Email already verified, proceeding to dashboard');
        // Clear any verification flow flag
        await AsyncStorage.removeItem('verification_flow');
        
        // Email is already verified, navigate to dashboard
        navigation.reset({
          index: 0,
          routes: [{ name: 'PersonalTabs' }],
        });
      }
    } catch (err: unknown) {
      // Clear any tokens since login failed
      await AsyncStorage.removeItem('token');
      await AsyncStorage.removeItem('userToken');
      await AsyncStorage.removeItem('accessToken');
      
      console.error('❌ Login failed:', err);
      
      if (axios.isAxiosError(err)) {
        // Check for specific error conditions
        if (err.response?.status === 401) {
          Alert.alert('Invalid Credentials', 'Please check your email and password and try again.');
        } else if (err.response?.status === 429) {
          Alert.alert('Too Many Attempts', 'Please wait a moment before trying again.');
        } else if (!err.response) {
          Alert.alert('Connection Error', 'Could not connect to the server. Please check your internet connection and try again.');
        } else {
          // Get error message from response
          const msg =
            err.response?.data?.message ??
            err.response?.data ??
            err.message ??
            'Login failed';
          Alert.alert('Login Error', typeof msg === 'string' ? msg : JSON.stringify(msg));
        }
      } else if (err instanceof Error) {
        Alert.alert('Error', err.message);
      } else {
        Alert.alert('Error', 'Something went wrong during login. Please try again.');
      }
    } finally {
      setLoading(false);
    }
  };

  const navigateToRegister = () => {
    navigation.navigate('Register');
  };

  return (
    <ImageBackground source={require('../assets/pic4.jpg')} style={styles.container}>
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', width: '100%' }}>
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : undefined}
          keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 20}
          style={{ width: '100%' }}
        >
          <View style={styles.formContainer}>
            <View style={styles.avatarPlaceholder}>
              <Image source={require('../assets/purrfectpaw_logo.png')} style={styles.avatarImage} />
            </View>
            <Text style={styles.title}>Welcome back!</Text>

            <TextInput
              style={styles.input}
              placeholder="Email"
              keyboardType="email-address"
              autoCapitalize="none"
              value={email}
              onChangeText={setEmail}
            />

            <View style={styles.inputWrapper}>
              <TextInput
                style={styles.input}
                placeholder="Password"
                secureTextEntry={!showPassword}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowPassword((v) => !v)}>
                <Ionicons name={showPassword ? 'eye' : 'eye-off'} size={24} color="#666" />
              </TouchableOpacity>
            </View>

            <TouchableOpacity style={styles.primaryButton} onPress={handleLogin} disabled={loading}>
              {loading ? <ActivityIndicator color="#000" /> : <Text style={styles.primaryButtonText}>Log in</Text>}
            </TouchableOpacity>
            
            {/* Register Link */}
            <View style={styles.registerLinkContainer}>
              <Text style={styles.registerText}>Don't have an account? </Text>
              <TouchableOpacity onPress={navigateToRegister}>
                <Text style={styles.registerLink}>Register here</Text>
              </TouchableOpacity>
            </View>
          </View>
        </KeyboardAvoidingView>
      </View>

      {loading && (
        <View style={styles.loadingOverlay}>
          <ActivityIndicator size="large" color={PINK} />
        </View>
      )}
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: PINK,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  formContainer: {
    width: '100%',
    backgroundColor: 'rgba(255,255,255,0.8)',
    borderRadius: 20,
    padding: 30,
    alignItems: 'center',
    elevation: 5,
  },
  title: { fontSize: 28, fontWeight: 'bold', color: DARK, marginBottom: 20 },
  input: {
    width: '100%',
    backgroundColor: '#fff',
    borderRadius: 30,
    paddingHorizontal: 20,
    paddingRight: 50,
    paddingVertical: 14,
    marginBottom: 10,
    fontSize: 16,
  },
  inputWrapper: { width: '100%', position: 'relative', marginBottom: 16 },
  eyeIcon: { position: 'absolute', right: 20, top: '50%', transform: [{ translateY: -12 }], zIndex: 1 },
  primaryButton: { backgroundColor: PINK, width: '100%', borderRadius: 30, paddingVertical: 14, alignItems: 'center', marginBottom: 12 },
  primaryButtonText: { color: '#000', fontSize: 16, fontWeight: 'bold' },
  registerLinkContainer: {
    flexDirection: 'row',
    marginTop: 10,
  },
  registerText: {
    color: DARK,
  },
  registerLink: {
    color: PINK,
    fontWeight: 'bold',
  },
  loadingOverlay: { ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(0,0,0,0.2)', justifyContent: 'center', alignItems: 'center' },
  avatarPlaceholder: { width: 100, height: 100, borderRadius: 50, backgroundColor: PINK, justifyContent: 'center', alignItems: 'center', marginBottom: 20 },
  avatarImage: { width: 85, height: 85, borderRadius: 30 },
});
