import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  RefreshControl,
  TouchableOpacity,
  Alert
} from 'react-native';
import { getAvailabilitySummary, AvailabilitySummary as AvailabilitySummaryType } from '../src/api';
import AvailabilitySummary from '../src/components/AvailabilitySummary';
import { Ionicons } from '@expo/vector-icons';

// Define types for navigation
type RootStackParamList = {
  ClinicAvailability: { clinicId?: number };
  BookAppointment: { clinicId: number };
  ClinicCalendar: { clinicId: number };
};

type Props = {
  route: { params?: { clinicId?: number } };
  navigation: {
    navigate: (screen: keyof RootStackParamList, params?: any) => void;
  };
};

const ClinicAvailabilityScreen: React.FC<Props> = ({ route, navigation }) => {
  const { clinicId = 1 } = route.params || {}; // Default to clinic ID 1 if not provided
  const [availabilityData, setAvailabilityData] = useState<AvailabilitySummaryType | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchAvailabilityData = async () => {
    try {
      setError(null);
      setIsLoading(true);
      
      console.log(`Fetching availability data for clinic ${clinicId}`);
      const data = await getAvailabilitySummary(clinicId);
      
      console.log('Availability data received:', JSON.stringify({
        today_slots: data.today?.remaining_slots,
        is_closed: data.today?.is_closed,
        next_week_count: data.next_week?.length
      }));
      
      setAvailabilityData(data);
    } catch (err) {
      console.error('Error fetching availability:', err);
      setError('Could not load clinic availability. Please try again later.');
    } finally {
      setIsLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchAvailabilityData();
    setRefreshing(false);
  };

  useEffect(() => {
    fetchAvailabilityData();
  }, [clinicId]);

  const navigateToBookAppointment = () => {
    if (!availabilityData || availabilityData.today.is_closed) {
      Alert.alert(
        "Clinic Closed",
        "The clinic is currently closed. Please check available days in the calendar.",
        [{ text: "OK" }]
      );
      return;
    }
    
    if (availabilityData.today.remaining_slots <= 0) {
      Alert.alert(
        "No Available Slots",
        "There are no available appointment slots for today. Please select another day.",
        [{ text: "OK" }]
      );
      return;
    }
    
    navigation.navigate('BookAppointment', { clinicId });
  };

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <View style={styles.header}>
        <Text style={styles.title}>Clinic Availability</Text>
        <View style={styles.buttonContainer}>
          {!isLoading && !error && (
            <>
              <TouchableOpacity 
                style={[styles.bookButton, { marginRight: 8 }]}
                onPress={navigateToBookAppointment}
              >
                <Ionicons name="calendar" size={18} color="white" />
                <Text style={styles.bookButtonText}>Book Appointment</Text>
              </TouchableOpacity>
              
              <TouchableOpacity 
                style={[styles.calendarButton]}
                onPress={() => navigation.navigate('ClinicCalendar', { clinicId })}
              >
                <Ionicons name="calendar-outline" size={18} color="white" />
                <Text style={styles.bookButtonText}>View Calendar</Text>
              </TouchableOpacity>
            </>
          )}
        </View>
      </View>

      <AvailabilitySummary 
        availabilityData={availabilityData}
        isLoading={isLoading}
        error={error}
      />
      
      {!isLoading && !error && (
        <View style={styles.infoContainer}>
          <Text style={styles.infoTitle}>Appointment Information</Text>
          <View style={styles.infoItem}>
            <Ionicons name="time-outline" size={20} color="#4f46e5" />
            <Text style={styles.infoText}>
              Appointments are {availabilityData?.settings?.slot_duration || 30} minutes long
            </Text>
          </View>
          <View style={styles.infoItem}>
            <Ionicons name="information-circle-outline" size={20} color="#4f46e5" />
            <Text style={styles.infoText}>
              Please arrive 10 minutes before your scheduled appointment time
            </Text>
          </View>
          <View style={styles.infoItem}>
            <Ionicons name="alert-circle-outline" size={20} color="#4f46e5" />
            <Text style={styles.infoText}>
              Cancellations should be made at least 24 hours in advance
            </Text>
          </View>
        </View>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
    padding: 16,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827',
  },
  buttonContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  bookButton: {
    flexDirection: 'row',
    backgroundColor: '#4f46e5',
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    alignItems: 'center',
    gap: 8,
  },
  calendarButton: {
    flexDirection: 'row',
    backgroundColor: '#4CAF50',
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    alignItems: 'center',
    gap: 8,
  },
  bookButtonText: {
    color: 'white',
    fontWeight: '600',
    fontSize: 14,
  },
  infoContainer: {
    backgroundColor: 'white',
    borderRadius: 12,
    padding: 16,
    marginTop: 20,
    marginBottom: 30,
  },
  infoTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#111827',
  },
  infoItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    gap: 8,
  },
  infoText: {
    fontSize: 14,
    color: '#4b5563',
    flex: 1,
  },
});

export default ClinicAvailabilityScreen;
