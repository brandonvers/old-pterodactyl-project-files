import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import NotFound from '@/components/screens/NotFound';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';

import BillingContainer from '@/components/dashboard/billing/BillingContainer';
import StoreContainer from '@/components/dashboard/billing/StoreContainer';
import CategoryContainer from '@/components/dashboard/billing/CategoryContainer';
import InvoicesContainer from '@/components/dashboard/billing/InvoicesContainer';
import CheckoutContainer from '@/components/dashboard/billing/CheckoutContainer';

export default ({ location }: RouteComponentProps) => (
    <>
        <NavigationBar/>
        {location.pathname.startsWith('/billing') &&
        <SubNavigation>
            <div>
                <NavLink to={'/billing'} exact>Billing</NavLink>
                <NavLink to={'/billing/store'} exact>Store</NavLink>
                <NavLink to={'/billing/invoices'} exact>Invoices</NavLink>
            </div>
        </SubNavigation>
        }
        <TransitionRouter>
            <Switch location={location}>
                <Route path={'/billing'} component={BillingContainer} exact/>
                <Route path={'/billing/store'} component={StoreContainer} exact/>
                <Route path={`/billing/store/category/:id`} component={CategoryContainer} exact/>
                <Route path={'/billing/store/checkout'} component={CheckoutContainer} exact/>
                <Route path={'/billing/invoices'} component={InvoicesContainer} exact/>
                <Route path={'*'} component={NotFound}/>
            </Switch>
        </TransitionRouter>
    </>
);
