// import React, { useState, useRef, useEffect } from 'react';
// import {
//   View,
//   Text,
//   TextInput,
//   TouchableOpacity,
//   StyleSheet,
//   Alert,
//   ActivityIndicator,
//   Image,
//   ScrollView,
//   KeyboardAvoidingView,
//   Keyboard,
//   Platform,
//   TouchableWithoutFeedback,
//   Modal,
//   Animated,
//   Dimensions,
// } from 'react-native';
// import { useNavigation } from '@react-navigation/native';
// import { NativeStackNavigationProp } from '@react-navigation/native-stack';
// import { RootStackParamList } from '../App';
// import DateTimePicker from '@react-native-community/datetimepicker';
// import { Ionicons } from '@expo/vector-icons';
// import api from '../src/api';
// import axios from 'axios';

// // Define our main colors
// const PINK = '#FF9EB1';
// const DARK = '#3A3A3A';

// export default function RegisterScreen(): React.ReactElement {
//   // Form state
//   const [firstName, setFirstName] = useState<string>('');
//   const [middleName, setMiddleName] = useState<string>('');
//   const [lastName, setLastName] = useState<string>('');
//   const [email, setEmail] = useState<string>('');
//   const [phoneNumber, setPhoneNumber] = useState<string>('');
//   const [password, setPassword] = useState<string>('');
//   const [passwordConfirmation, setPasswordConfirmation] = useState<string>('');
//   const [gender, setGender] = useState<string>('');
//   const [birthday, setBirthday] = useState<Date | null>(null);
  
//   // UI state
//   const [showPassword, setShowPassword] = useState<boolean>(false);
//   const [showPasswordConfirmation, setShowPasswordConfirmation] = useState<boolean>(false);
//   const [loading, setLoading] = useState<boolean>(false);
//   const [genderModalVisible, setGenderModalVisible] = useState<boolean>(false);
//   const [showDatePicker, setShowDatePicker] = useState<boolean>(false);
  
//   // Focus state for inputs
//   const [firstNameFocused, setFirstNameFocused] = useState<boolean>(false);
//   const [middleNameFocused, setMiddleNameFocused] = useState<boolean>(false);
//   const [lastNameFocused, setLastNameFocused] = useState<boolean>(false);
//   const [emailFocused, setEmailFocused] = useState<boolean>(false);
//   const [phoneNumberFocused, setPhoneNumberFocused] = useState<boolean>(false);
//   const [passwordFocused, setPasswordFocused] = useState<boolean>(false);
//   const [confirmPasswordFocused, setConfirmPasswordFocused] = useState<boolean>(false);

//   // Refs
//   const scrollViewRef = useRef<ScrollView>(null);
  
//   // Navigation
//   const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  
//   // Animation values
//   const fadeAnim = useRef(new Animated.Value(0)).current;
//   const slideAnim = useRef(new Animated.Value(30)).current;
  
//   const handleScreenPress = () => {
//     Keyboard.dismiss();
//   };

//   // Animation when component mounts
//   useEffect(() => {
//     Animated.parallel([
//       Animated.timing(fadeAnim, {
//         toValue: 1,
//         duration: 800,
//         useNativeDriver: true
//       }),
//       Animated.timing(slideAnim, {
//         toValue: 0,
//         duration: 600,
//         useNativeDriver: true
//       })
//     ]).start();
//   }, []);

//   const handleRegister = async () => {
//     // Validate inputs
//     if (!firstName.trim()) {
//       Alert.alert('Error', 'First name is required');
//       return;
//     }
//     if (!lastName.trim()) {
//       Alert.alert('Error', 'Last name is required');
//       return;
//     }
//     if (!email.trim()) {
//       Alert.alert('Error', 'Email is required');
//       return;
//     }
//     // Basic email validation
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     if (!emailRegex.test(email)) {
//       Alert.alert('Error', 'Please enter a valid email');
//       return;
//     }
//     if (!password) {
//       Alert.alert('Error', 'Password is required');
//       return;
//     }
//     if (password.length < 8) {
//       Alert.alert('Error', 'Password must be at least 8 characters');
//       return;
//     }
//     if (password !== passwordConfirmation) {
//       Alert.alert('Error', 'Passwords do not match');
//       return;
//     }
//     if (!gender) {
//       Alert.alert('Error', 'Gender is required');
//       return;
//     }
//     if (!birthday) {
//       Alert.alert('Error', 'Birthday is required');
//       return;
//     }

//     // Proceed with registration
//     setLoading(true);

//     try {
//       // Format the birthday to YYYY-MM-DD
//       const formattedBirthday = birthday.toISOString().split('T')[0];

//       const response = await api.post('/auth/register', {
//         first_name: firstName,
//         middle_name: middleName || '',
//         last_name: lastName,
//         email: email,
//         password: password,
//         password_confirmation: passwordConfirmation,
//         gender: gender,
//         birthday: formattedBirthday,
//         phone: phoneNumber || ''
//       });

//       if (response.data.success) {
//         // Check if verification required
//         if (response.data.verification_required) {
//           Alert.alert(
//             'Registration Successful',
//             'Please verify your email to activate your account.',
//             [
//               {
//                 text: 'OK',
//                 onPress: () => {
//                   // Navigate to OTP verification screen
//                   navigation.navigate('OTPVerification', { email });
//                 }
//               }
//             ]
//           );
//         }
//       }
//     } catch (err: unknown) {
//       if (axios.isAxiosError(err)) {
//         const errorData = err.response?.data;
//         let errorMessage = 'Registration failed';

//         if (errorData?.errors) {
//           // Laravel validation errors
//           const errors = Object.values(errorData.errors).flat();
//           errorMessage = errors.join('\n');
//         } else if (errorData?.message) {
//           errorMessage = errorData.message;
//         }

//         Alert.alert('Error', errorMessage);
//       } else if (err instanceof Error) {
//         Alert.alert('Error', err.message);
//       } else {
//         Alert.alert('Error', 'Something went wrong');
//       }
//     } finally {
//       setLoading(false);
//     }
//   };

//   const navigateToLogin = () => {
//     // Animate out before navigating
//     Animated.parallel([
//       Animated.timing(fadeAnim, {
//         toValue: 0,
//         duration: 300,
//         useNativeDriver: true
//       }),
//       Animated.timing(slideAnim, {
//         toValue: -30,
//         duration: 300,
//         useNativeDriver: true
//       })
//     ]).start(() => {
//       navigation.navigate('Login');
//     });
//   };

//   return (
//     <TouchableOpacity 
//       activeOpacity={1} 
//       style={styles.container}
//       onPress={handleScreenPress}
//     >
//       {/* Wave background at the top */}
//       <View style={styles.topWave}>
//         <Image 
//           source={require('../assets/pic4.jpg')} 
//           style={styles.wavePattern}
//           resizeMode="cover"
//         />
//       </View>

//       <KeyboardAvoidingView
//         behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
//         keyboardVerticalOffset={Platform.OS === 'ios' ? 40 : 0}
//         style={styles.formWrapper}
//       >
//         <ScrollView
//           ref={scrollViewRef}
//           showsVerticalScrollIndicator={false}
//           contentContainerStyle={styles.scrollViewContent}
//           keyboardShouldPersistTaps="handled"
//         >
//           <Animated.View 
//             style={[
//               styles.formContainer,
//               { opacity: fadeAnim, transform: [{ translateY: slideAnim }] }
//             ]}
//           >
//             <View style={styles.headerContainer}>
//               <View style={styles.logoContainer}>
//                 <Image source={require('../assets/purrfectpaw_logo.png')} style={styles.logoImage} />
//               </View>
//               <Text style={styles.headerTitle}>Create Account</Text>
//               <Text style={styles.headerSubtitle}>Join PurrfectPaw today</Text>
//             </View>

//             {/* First Name Input */}
//             <TextInput
//               style={styles.input}
//               placeholder="First Name *"
//               autoCapitalize="words"
//               value={firstName}
//               onChangeText={setFirstName}
//             />

//             {/* Middle Name Input */}
//             <TextInput
//               style={styles.input}
//               placeholder="Middle Name (optional)"
//               autoCapitalize="words"
//               value={middleName}
//               onChangeText={setMiddleName}
//             />

//             {/* Last Name Input */}
//             <TextInput
//               style={styles.input}
//               placeholder="Last Name *"
//               autoCapitalize="words"
//               value={lastName}
//               onChangeText={setLastName}
//             />

//             {/* Email Input */}
//             <TextInput
//               style={styles.input}
//               placeholder="Email *"
//               keyboardType="email-address"
//               autoCapitalize="none"
//               value={email}
//               onChangeText={setEmail}
//             />

//             {/* Phone Number Input */}
//             <TextInput
//               style={styles.input}
//               placeholder="Phone Number"
//               keyboardType="phone-pad"
//               value={phoneNumber}
//               onChangeText={setPhoneNumber}
//             />

//             {/* Gender Selection */}
//             <TouchableOpacity 
//               style={styles.genderButton} 
//               onPress={() => setGenderModalVisible(true)}
//             >
//               <Text style={[styles.genderText, {color: gender ? DARK : '#a0a0a0'}]}>
//                 {gender ? 
//                  (gender === 'female' ? 'Female' : 
//                   gender === 'male' ? 'Male' : 
//                   'Prefer not to say') : 
//                  'Select Gender *'}
//               </Text>
//               <Text>▼</Text>
//             </TouchableOpacity>

//             {/* Birthday Selector */}
//             <TouchableOpacity 
//               style={styles.datePickerButton}
//               onPress={() => setShowDatePicker(true)}
//             >
//               <Text style={[styles.dateText, {color: birthday ? DARK : '#a0a0a0'}]}>
//                 {birthday ? birthday.toLocaleDateString() : 'Select Birthday *'}
//               </Text>
//               <Text>📅</Text>
//             </TouchableOpacity>

//             {/* Password Input */}
//             <View style={styles.inputWrapper}>
//               <TextInput
//                 style={styles.inputField}
//                 placeholder="Password *"
//                 secureTextEntry={!showPassword}
//                 value={password}
//                 onChangeText={setPassword}
//               />
//               <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowPassword((v) => !v)}>
//                 <Ionicons name={showPassword ? 'eye' : 'eye-off'} size={24} color="#666" />
//               </TouchableOpacity>
//             </View>

//             {/* Confirm Password Input */}
//             <View style={styles.inputWrapper}>
//               <TextInput
//                 style={styles.inputField}
//                 placeholder="Confirm Password"
//                 secureTextEntry={!showPasswordConfirmation}
//                 value={passwordConfirmation}
//                 onChangeText={setPasswordConfirmation}
//               />
//               <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowPasswordConfirmation((v) => !v)}>
//                 <Ionicons name={showPasswordConfirmation ? 'eye' : 'eye-off'} size={24} color="#666" />
//               </TouchableOpacity>
//             </View>

//             {/* Register Button */}
//             <TouchableOpacity style={styles.primaryButton} onPress={handleRegister} disabled={loading}>
//               {loading ? <ActivityIndicator color="#000" /> : <Text style={styles.primaryButtonText}>Register</Text>}
//             </TouchableOpacity>

//             {/* Login Link */}
//             <View style={styles.loginLinkContainer}>
//               <Text style={styles.loginText}>Already registered? </Text>
//               <TouchableOpacity onPress={navigateToLogin}>
//                 <Text style={styles.loginLink}>Login here</Text>
//               </TouchableOpacity>
//             </View>
//           </Animated.View>
//         </ScrollView>
//       </KeyboardAvoidingView>

//       {loading && (
//         <View style={styles.loadingOverlay}>
//           <ActivityIndicator size="large" color={PINK} />
//         </View>
//       )}

//       {/* Gender Selection Modal */}
//       <Modal
//         animationType="slide"
//         transparent={true}
//         visible={genderModalVisible}
//         onRequestClose={() => setGenderModalVisible(false)}
//       >
//         <View style={styles.modalOverlay}>
//           <View style={styles.modalContent}>
//             <Text style={styles.modalTitle}>Select Gender</Text>
            
//             <TouchableOpacity 
//               style={styles.modalOption}
//               onPress={() => {
//                 setGender('female');
//                 setGenderModalVisible(false);
//               }}
//             >
//               <Text style={styles.modalOptionText}>Female</Text>
//             </TouchableOpacity>
            
//             <TouchableOpacity 
//               style={styles.modalOption}
//               onPress={() => {
//                 setGender('male');
//                 setGenderModalVisible(false);
//               }}
//             >
//               <Text style={styles.modalOptionText}>Male</Text>
//             </TouchableOpacity>
            
//             <TouchableOpacity 
//               style={styles.modalOption}
//               onPress={() => {
//                 setGender('prefer_not_say');
//                 setGenderModalVisible(false);
//               }}
//             >
//               <Text style={styles.modalOptionText}>Prefer not to say</Text>
//             </TouchableOpacity>
            
//             <TouchableOpacity 
//               style={[styles.modalOption, {backgroundColor: PINK}]}
//               onPress={() => setGenderModalVisible(false)}
//             >
//               <Text style={[styles.modalOptionText, {fontWeight: 'bold'}]}>Cancel</Text>
//             </TouchableOpacity>
//           </View>
//         </View>
//       </Modal>

//       {/* Date Picker for Birthday */}
//       {showDatePicker && (
//         <DateTimePicker
//           value={birthday || new Date()}
//           mode="date"
//           display="default"
//           onChange={(event, selectedDate) => {
//             setShowDatePicker(false);
//             if (selectedDate && event.type !== 'dismissed') {
//               setBirthday(selectedDate);
//             }
//           }}
//           maximumDate={new Date()} // Cannot select future dates
//         />
//       )}
//     </TouchableOpacity>
//   );
// }

// const styles = StyleSheet.create({
//   container: {
//     flex: 1,
//     backgroundColor: '#FFFFFF',
//   },
//   // Top wave shape with background image
//   topWave: {
//     height: 150,
//     width: '100%',
//     backgroundColor: '#FFF',
//     borderBottomLeftRadius: 0,
//     borderBottomRightRadius: 0,
//     overflow: 'hidden',
//   },
//   wavePattern: {
//     width: '100%',
//     height: '100%',
//   },
//   formWrapper: {
//     flex: 1,
//     paddingHorizontal: 24,
//     paddingTop: 10,
//   },
//   // Scroll view content
//   scrollViewContent: {
//     paddingBottom: Platform.OS === 'ios' ? 120 : 80, // Extra padding at bottom to ensure form is fully scrollable
//   },
//   // Form container
//   formContainer: {
//     width: '100%',
//   },
//   // Header styling
//   headerContainer: {
//     marginBottom: 30,
//     alignItems: 'center',
//   },
//   logoContainer: {
//     width: 80,
//     height: 80,
//     borderRadius: 40,
//     backgroundColor: '#FFFFFF',
//     justifyContent: 'center',
//     alignItems: 'center',
//     marginBottom: 16,
//     shadowColor: '#000',
//     shadowOffset: { width: 0, height: 2 },
//     shadowOpacity: 0.1,
//     shadowRadius: 4,
//     elevation: 3,
//   },
//   logoImage: {
//     width: 60,
//     height: 60,
//     resizeMode: 'contain',
//   },
//   headerTitle: {
//     fontSize: 28,
//     fontWeight: 'bold',
//     color: DARK,
//     marginBottom: 6,
//     textAlign: 'center',
//   },
//   headerSubtitle: {
//     fontSize: 16,
//     color: '#666',
//     textAlign: 'center',
//     marginBottom: 10,
//   },
//   // Text input styling
//   inputWrapper: {
//     width: '100%',
//     position: 'relative',
//     borderWidth: 1.2,
//     borderColor: '#E8E8E8',
//     borderRadius: 12,
//     backgroundColor: '#FFFFFF',
//     shadowColor: '#000',
//     shadowOffset: { width: 0, height: 2 },
//     shadowOpacity: 0.06,
//     shadowRadius: 3,
//     elevation: 1,
//     marginBottom: 16,
//   },
//   inputField: {
//     width: '100%',
//     paddingHorizontal: 18,
//     paddingVertical: 15,
//     fontSize: 16,
//     color: DARK,
//   },
//   input: {
//     width: '100%',
//     paddingHorizontal: 18,
//     paddingVertical: 15,
//     fontSize: 16,
//     color: DARK,
//     borderWidth: 1.2,
//     borderColor: '#E8E8E8',
//     borderRadius: 12,
//     backgroundColor: '#FFFFFF',
//     marginBottom: 16,
//   },
//   // Gender selection
//   genderButton: {
//     backgroundColor: '#fff',
//     borderRadius: 12,
//     paddingHorizontal: 18,
//     paddingVertical: 15,
//     width: '100%',
//     flexDirection: 'row',
//     justifyContent: 'space-between',
//     alignItems: 'center',
//     borderWidth: 1.2,
//     borderColor: '#E8E8E8',
//     marginBottom: 16,
//   },
//   genderText: {
//     fontSize: 16,
//   },
//   // Date picker button
//   datePickerButton: {
//     backgroundColor: '#fff',
//     borderRadius: 12,
//     paddingHorizontal: 18,
//     paddingVertical: 15,
//     width: '100%',
//     flexDirection: 'row',
//     justifyContent: 'space-between',
//     alignItems: 'center',
//     borderWidth: 1.2,
//     borderColor: '#E8E8E8',
//     marginBottom: 16,
//   },
//   dateText: {
//     fontSize: 16,
//   },
//   // Password input
//   eyeIcon: {
//     position: 'absolute',
//     right: 16,
//     top: 15,
//   },
//   // Primary button
//   primaryButton: {
//     backgroundColor: PINK,
//     width: '100%',
//     borderRadius: 14,
//     paddingVertical: 16,
//     alignItems: 'center',
//     marginBottom: 24,
//     marginTop: 10,
//     shadowColor: PINK,
//     shadowOffset: { width: 0, height: 6 },
//     shadowOpacity: 0.35,
//     shadowRadius: 12,
//     elevation: 6,
//   },
//   primaryButtonText: {
//     color: '#FFFFFF',
//     fontSize: 17,
//     fontWeight: 'bold',
//     letterSpacing: 0.5,
//   },
//   // Login link
//   loginLinkContainer: {
//     flexDirection: 'row',
//     justifyContent: 'center',
//     alignItems: 'center',
//     marginTop: 5,
//   },
//   loginText: {
//     fontSize: 15,
//     color: '#666',
//   },
//   loginLink: {
//     fontSize: 15,
//     color: PINK,
//     fontWeight: '500',
//   },
//   // Loading overlay
//   loadingOverlay: {
//     ...StyleSheet.absoluteFillObject,
//     backgroundColor: 'rgba(255,255,255,0.7)',
//     justifyContent: 'center',
//     alignItems: 'center',
//   },
//   // Modal styling
//   modalOverlay: {
//     flex: 1,
//     backgroundColor: 'rgba(0,0,0,0.5)',
//     justifyContent: 'center',
//     alignItems: 'center',
//   },
//   modalContent: {
//     width: '80%',
//     backgroundColor: 'white',
//     borderRadius: 20,
//     padding: 20,
//     shadowColor: '#000',
//     shadowOffset: {
//       width: 0,
//       height: 2,
//     },
//     shadowOpacity: 0.25,
//     shadowRadius: 4,
//     elevation: 5,
//   },
//   modalTitle: {
//     fontSize: 18,
//     fontWeight: 'bold',
//     color: DARK,
//     textAlign: 'center',
//     marginBottom: 20,
//   },
//   modalOption: {
//     paddingVertical: 15,
//     paddingHorizontal: 12,
//     borderRadius: 10,
//     marginBottom: 8,
//     backgroundColor: '#f8f8f8',
//   },
//   modalOptionText: {
//     fontSize: 16,
//     textAlign: 'center',
//     color: DARK,
//   },
// });