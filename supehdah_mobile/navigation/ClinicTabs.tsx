import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import ClinicHomeScreen from '../screens/ClinicHomeScreen';
import ClinicAppointmentsScreen from '../screens/ClinicAppointmentsScreen';
import ClinicGalleryScreen from '../screens/ClinicGalleryScreen';
import ClinicSettingsScreen from '../screens/ClinicSettingsScreen';
import ClinicCalendarScreen from '../screens/ClinicCalendarScreen';

const Tab = createBottomTabNavigator();

export default function ClinicTabs() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarIcon: ({ color, size }) => {
          let iconName: keyof typeof Ionicons.glyphMap = 'home';
          if (route.name === 'ClinicHome') iconName = 'business';
          else if (route.name === 'ClinicAppointments') iconName = 'calendar';
          else if (route.name === 'ClinicCalendar') iconName = 'calendar-outline';
          else if (route.name === 'ClinicGallery') iconName = 'images';
          else if (route.name === 'ClinicSettings') iconName = 'settings';
          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#FFC1CC',
        tabBarInactiveTintColor: '#333',
      })}
    >
      <Tab.Screen name="ClinicHome" component={ClinicHomeScreen} />
      <Tab.Screen 
        name="ClinicAppointments" 
        component={ClinicAppointmentsScreen}
        options={{
          // Using an empty string instead of null for the badge
          // The badge will be updated when an appointment is selected
        }}
      />
      <Tab.Screen 
        name="ClinicCalendar" 
        component={ClinicCalendarScreen}
        options={{ 
          title: 'Availability'
        }}
      />
      <Tab.Screen name="ClinicGallery" component={ClinicGalleryScreen} />
      <Tab.Screen name="ClinicSettings" component={ClinicSettingsScreen} />
    </Tab.Navigator>
  );
} 