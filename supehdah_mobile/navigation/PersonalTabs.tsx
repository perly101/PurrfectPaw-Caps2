import React, { useEffect, useState } from 'react';
import { View, Platform, StyleSheet, Text, Dimensions } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EdgeInsets } from 'react-native-safe-area-context';
import HomeScreen from '../screens/HomeScreen';
import AppointmentsScreen from '../screens/AppointmentsScreen';
import SettingsScreen from '../screens/SettingsScreen';
import NotificationsScreen from '../screens/NotificationsScreen';
import { Ionicons } from '@expo/vector-icons';
import NotificationBadge from '../components/NotificationBadge';

// Define our main colors
const PINK = '#FF9EB1';
const DARK = '#333333';

const Tab = createBottomTabNavigator();

export default function PersonalTabs() {
  const insets = useSafeAreaInsets();
  const [notificationCount, setNotificationCount] = useState(0);
  const screenHeight = Dimensions.get('window').height;
  
  // Determine if device has hardware buttons (estimation)
  const hasHardwareButtons = Platform.OS === 'android' && !insets.bottom;
  
  // Simulate fetching notification count (replace with actual API call)
  useEffect(() => {
    // You would normally fetch this from your API
    setNotificationCount(2);
  }, []);
  
  // Custom tab bar icon with badge
  const renderTabBarIcon = (routeName: string, focused: boolean) => {
    let iconName: keyof typeof Ionicons.glyphMap = 'home';
    
    switch (routeName) {
      case 'Home':
        iconName = focused ? 'home' : 'home-outline';
        break;
      case 'Appointments':
        iconName = focused ? 'calendar' : 'calendar-outline';
        break;
      case 'Notifications':
        iconName = focused ? 'notifications' : 'notifications-outline';
        return (
          <View style={styles.iconContainer}>
            <Ionicons name={iconName} size={24} color={focused ? PINK : '#888'} />
            {notificationCount > 0 && (
              <View style={styles.badge}>
                <Text style={styles.badgeText}>{notificationCount}</Text>
              </View>
            )}
          </View>
        );
      case 'Settings':
        iconName = focused ? 'settings' : 'settings-outline';
        break;
    }
    
    return <Ionicons name={iconName} size={24} color={focused ? PINK : '#888'} />;
  };

  // Calculate safe bottom margin for Android devices with hardware navigation buttons
  const getBottomMargin = () => {
    if (Platform.OS === 'ios') return insets.bottom;
    
    // For Android: add extra padding if device likely has hardware navigation buttons
    if (hasHardwareButtons) {
      return 25; // Extra space for hardware buttons
    } else {
      return 15; // Standard padding for Android devices with gesture navigation
    }
  };

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused }) => renderTabBarIcon(route.name, focused),
        tabBarActiveTintColor: PINK,
        tabBarInactiveTintColor: '#888',
        tabBarShowLabel: true,
        tabBarStyle: {
          height: 65 + (Platform.OS === 'ios' ? insets.bottom : getBottomMargin()),
          paddingBottom: getBottomMargin(),
          paddingTop: 8,
          backgroundColor: '#FFFFFF',
          borderTopWidth: 0,
          borderTopColor: 'transparent',
          elevation: 15,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -3 },
          shadowOpacity: 0.1,
          shadowRadius: 12,
          borderTopLeftRadius: 25,
          borderTopRightRadius: 25,
          position: 'absolute',
          left: 0,
          right: 0,
          bottom: 0,
          marginBottom: hasHardwareButtons ? 5 : 0, // Additional margin for hardware buttons
        },
        headerShown: false,
        tabBarHideOnKeyboard: true, // Hide tab bar when keyboard is visible
        tabBarItemStyle: {
          paddingVertical: hasHardwareButtons ? 8 : 5,
          height: hasHardwareButtons ? 55 : 50,
          borderRadius: 15,
          margin: 5,
          marginBottom: hasHardwareButtons ? 10 : 5,
        },
        tabBarLabelStyle: {
          fontWeight: '600',
          fontSize: 12,
          marginTop: -5,
          marginBottom: 5,
        },
      })}
    >
      <Tab.Screen 
        name="Home" 
        component={HomeScreen}
        options={{
          tabBarLabel: 'Home',
        }}
      />
      <Tab.Screen 
        name="Appointments" 
        component={AppointmentsScreen}
        options={{
          tabBarLabel: 'Appointments',
        }}
      />
      <Tab.Screen 
        name="Notifications" 
        component={NotificationsScreen}
        options={{
          tabBarLabel: 'Notifications',
        }}
      />
      <Tab.Screen 
        name="Settings" 
        component={SettingsScreen}
        options={{
          tabBarLabel: 'Settings',
        }}
      />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  iconContainer: {
    width: 32,
    height: 32,
    alignItems: 'center',
    justifyContent: 'center',
  },
  badge: {
    position: 'absolute',
    right: -5,
    top: -2,
    backgroundColor: '#FF4757',
    borderRadius: 10,
    minWidth: 18,
    height: 18,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1.5,
    borderColor: '#FFF',
  },
  badgeText: {
    color: '#FFF',
    fontSize: 10,
    fontWeight: 'bold',
    paddingHorizontal: 3,
  },
  tabBarButton: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  activeTab: {
    backgroundColor: `${PINK}20`,
    borderRadius: 15,
  },
});