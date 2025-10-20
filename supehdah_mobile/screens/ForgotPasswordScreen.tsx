// screens/ForgotPasswordScreen.tsx
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
  SafeAreaView,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../App';
import { Ionicons } from '@expo/vector-icons';
import { API, forgotPassword } from '../src/api';

const PINK = '#FFC1CC';
const DARK = '#333';

export default function ForgotPasswordScreen(): React.ReactElement {
  const [email, setEmail] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(false);
  const [messageSent, setMessageSent] = useState<boolean>(false);
  
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();

  const handleResetPassword = async (): Promise<void> => {
    if (!email || !email.trim()) {
      Alert.alert('Email Required', 'Please enter your email address.');
      return;
    }

    setLoading(true);
    
    try {
      // Make API call to server's password reset endpoint
      const response = await forgotPassword(email.trim());
      
      console.log('Password reset response:', response);
      
      // Show success message
      setMessageSent(true);
      Alert.alert(
        'Reset Link Sent',
        'A password reset link has been sent to your email. Please check your inbox.',
        [{ text: 'OK' }]
      );
    } catch (error: any) {
      console.error('Password reset error:', error);
      
      // Handle specific error types
      let errorMessage = 'Failed to send password reset link. Please try again.';
      
      if (error.response) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        if (error.response.status === 422) {
          // Validation error
          errorMessage = error.response.data.message || 'Invalid email address.';
        } else if (error.response.status === 429) {
          errorMessage = 'Too many attempts. Please try again later.';
        } else if (error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
      } else if (error.request) {
        // The request was made but no response was received
        errorMessage = 'Network error. Please check your internet connection.';
      }
      
      Alert.alert('Error', errorMessage);
    } finally {
      setLoading(false);
    }
  };

  return (
    <ImageBackground source={require('../assets/pic0.jpg')} style={styles.container}>
      <SafeAreaView style={{ flex: 1, width: '100%' }}>
        {/* Back button */}
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        
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
              
              <Text style={styles.title}>Forgot Password</Text>
              
              {messageSent ? (
                <View style={styles.successContainer}>
                  <Ionicons name="checkmark-circle" size={60} color="#4CAF50" />
                  <Text style={styles.successText}>
                    A password reset link has been sent to your email.
                  </Text>
                  <Text style={styles.instructionsText}>
                    Please check your inbox and follow the instructions to reset your password.
                  </Text>
                  <TouchableOpacity 
                    style={[styles.primaryButton, { marginTop: 20 }]}
                    onPress={() => navigation.navigate('Login')}
                  >
                    <Text style={styles.primaryButtonText}>Back to Login</Text>
                  </TouchableOpacity>
                </View>
              ) : (
                <>
                  <Text style={styles.instructionsText}>
                    Please enter your email address, and we'll send you a link to reset your password.
                  </Text>
                  
                  <TextInput
                    style={styles.input}
                    placeholder="Email"
                    keyboardType="email-address"
                    autoCapitalize="none"
                    value={email}
                    onChangeText={setEmail}
                  />
                  
                  <TouchableOpacity 
                    style={styles.primaryButton} 
                    onPress={handleResetPassword} 
                    disabled={loading}
                  >
                    {loading ? (
                      <ActivityIndicator color="#000" />
                    ) : (
                      <Text style={styles.primaryButtonText}>Send Reset Link</Text>
                    )}
                  </TouchableOpacity>
                  
                  <TouchableOpacity 
                    style={styles.secondaryButton}
                    onPress={() => navigation.navigate('Login')}
                  >
                    <Text style={styles.secondaryButtonText}>Back to Login</Text>
                  </TouchableOpacity>
                </>
              )}
            </View>
          </KeyboardAvoidingView>
        </View>
      </SafeAreaView>

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
  backButton: {
    position: 'absolute',
    top: 20, 
    left: 20,
    zIndex: 10,
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255,255,255,0.8)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  formContainer: {
    width: '100%',
    backgroundColor: 'rgba(255,255,255,0.8)',
    borderRadius: 20,
    padding: 30,
    alignItems: 'center',
    elevation: 5,
  },
  title: { 
    fontSize: 28, 
    fontWeight: 'bold', 
    color: DARK, 
    marginBottom: 15 
  },
  instructionsText: {
    textAlign: 'center',
    marginBottom: 20,
    color: DARK,
    fontSize: 14,
    lineHeight: 20,
  },
  input: {
    width: '100%',
    backgroundColor: '#fff',
    borderRadius: 30,
    paddingHorizontal: 20,
    paddingVertical: 14,
    marginBottom: 20,
    fontSize: 16,
  },
  primaryButton: { 
    backgroundColor: PINK, 
    width: '100%', 
    borderRadius: 30, 
    paddingVertical: 14, 
    alignItems: 'center', 
    marginBottom: 12 
  },
  primaryButtonText: { 
    color: '#000', 
    fontSize: 16, 
    fontWeight: 'bold' 
  },
  secondaryButton: {
    width: '100%',
    borderRadius: 30,
    paddingVertical: 14,
    alignItems: 'center',
  },
  secondaryButtonText: {
    color: DARK,
    fontSize: 16,
  },
  loadingOverlay: { 
    ...StyleSheet.absoluteFillObject, 
    backgroundColor: 'rgba(0,0,0,0.2)', 
    justifyContent: 'center', 
    alignItems: 'center' 
  },
  avatarPlaceholder: { 
    width: 100, 
    height: 100, 
    borderRadius: 50, 
    backgroundColor: PINK, 
    justifyContent: 'center', 
    alignItems: 'center', 
    marginBottom: 20 
  },
  avatarImage: { 
    width: 85, 
    height: 85, 
    borderRadius: 30 
  },
  successContainer: {
    alignItems: 'center',
    paddingVertical: 20,
  },
  successText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: DARK,
    marginTop: 15,
    marginBottom: 10,
    textAlign: 'center',
  },
});